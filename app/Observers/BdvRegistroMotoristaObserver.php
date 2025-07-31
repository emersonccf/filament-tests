<?php

namespace App\Observers;

use App\Models\BdvRegistroMotorista;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BdvRegistroMotoristaObserver
{
    /**
     * Handle the BdvRegistroMotorista "created" event.
     * Executado quando um novo registro é criado
     */
    public function created(BdvRegistroMotorista $bdvRegistroMotorista): void
    {
        // Quando um registro é criado, verificamos se já tem km_chegada
        if ($bdvRegistroMotorista->km_chegada !== null) {
            $this->atualizarQuilometragemVeiculo($bdvRegistroMotorista);
        }
    }

    /**
     * Handle the BdvRegistroMotorista "updated" event.
     * Executado quando um registro existente é atualizado
     */
    public function updated(BdvRegistroMotorista $bdvRegistroMotorista): void
    {
        // Verifica se o campo km_chegada foi alterado
        if ($bdvRegistroMotorista->isDirty('km_chegada')) {
            $this->atualizarQuilometragemVeiculo($bdvRegistroMotorista);
        }
    }

    /**
     * Handle the BdvRegistroMotorista "deleted" event.
     * Executado quando um registro é deletado
     */
    public function deleted(BdvRegistroMotorista $bdvRegistroMotorista): void
    {
        // Quando um registro é deletado, podemos querer reverter a quilometragem
        // Isso é opcional e depende da regra de negócio
        $this->reverterQuilometragemVeiculo($bdvRegistroMotorista);
    }

    /**
     * Atualiza a quilometragem do veículo com base no km_chegada
     */
    private function atualizarQuilometragemVeiculo(BdvRegistroMotorista $bdvRegistroMotorista): void
    {
        // Validações iniciais
        if ($bdvRegistroMotorista->km_chegada === null) {
            return;
        }

        try {
            // Carrega o BDV principal com o veículo
            $bdvMain = $bdvRegistroMotorista->bdvMain()->with('veiculo')->first();

            if (!$bdvMain || !$bdvMain->veiculo) {
                Log::warning('BDV ou veículo não encontrado para atualização de quilometragem', [
                    'id_registro_motorista' => $bdvRegistroMotorista->id_registro_motorista,
                    'id_bdv' => $bdvRegistroMotorista->id_bdv
                ]);
                return;
            }

            $veiculo = $bdvMain->veiculo;
            $novaQuilometragem = $bdvRegistroMotorista->km_chegada;

            // Validação: nova quilometragem deve ser maior que a atual
            if ($veiculo->quilometragem !== null && $novaQuilometragem <= $veiculo->quilometragem) {
                Log::warning('Tentativa de atualizar quilometragem com valor menor ou igual ao atual', [
                    'id_veiculo' => $veiculo->id_veiculo,
                    'placa' => $veiculo->placa,
                    'quilometragem_atual' => $veiculo->quilometragem,
                    'nova_quilometragem' => $novaQuilometragem,
                    'id_registro_motorista' => $bdvRegistroMotorista->id_registro_motorista
                ]);
                return;
            }

            // Atualiza a quilometragem do veículo
            $quilometragemAnterior = $veiculo->quilometragem;

            $veiculo->update([
                'quilometragem' => $novaQuilometragem,
                'atualizado_por' => $bdvRegistroMotorista->atualizado_por ?? $bdvRegistroMotorista->cadastrado_por
            ]);

            // Log da atualização
            Log::info('Quilometragem do veículo atualizada automaticamente', [
                'id_veiculo' => $veiculo->id_veiculo,
                'placa' => $veiculo->placa,
                'quilometragem_anterior' => $quilometragemAnterior,
                'nova_quilometragem' => $novaQuilometragem,
                'diferenca' => $novaQuilometragem - ($quilometragemAnterior ?? 0),
                'id_registro_motorista' => $bdvRegistroMotorista->id_registro_motorista,
                'id_bdv' => $bdvRegistroMotorista->id_bdv
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar quilometragem do veículo', [
                'id_registro_motorista' => $bdvRegistroMotorista->id_registro_motorista,
                'km_chegada' => $bdvRegistroMotorista->km_chegada,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Reverte a quilometragem do veículo quando um registro é deletado
     * (Implementação opcional - depende da regra de negócio)
     */
    private function reverterQuilometragemVeiculo(BdvRegistroMotorista $bdvRegistroMotorista): void
    {
        // Esta implementação é opcional e depende se você quer reverter
        // a quilometragem quando um registro BDV é deletado

        if ($bdvRegistroMotorista->km_chegada === null) {
            return;
        }

        try {
            $bdvMain = $bdvRegistroMotorista->bdvMain()->with('veiculo')->first();

            if (!$bdvMain || !$bdvMain->veiculo) {
                return;
            }

            $veiculo = $bdvMain->veiculo;

            // Busca o último registro válido de quilometragem para este veículo
            $ultimoRegistro = BdvRegistroMotorista::whereHas('bdvMain', function ($query) use ($veiculo) {
                $query->where('id_veiculo', $veiculo->id_veiculo);
            })
                ->whereNotNull('km_chegada')
                ->where('id_registro_motorista', '!=', $bdvRegistroMotorista->id_registro_motorista)
                ->orderBy('momento_chegada', 'desc')
                ->first();

            if ($ultimoRegistro) {
                $veiculo->update([
                    'quilometragem' => $ultimoRegistro->km_chegada
                ]);

                Log::info('Quilometragem do veículo revertida após deleção de registro BDV', [
                    'id_veiculo' => $veiculo->id_veiculo,
                    'placa' => $veiculo->placa,
                    'quilometragem_revertida' => $ultimoRegistro->km_chegada,
                    'registro_deletado' => $bdvRegistroMotorista->id_registro_motorista
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao reverter quilometragem do veículo', [
                'id_registro_motorista' => $bdvRegistroMotorista->id_registro_motorista,
                'error' => $e->getMessage()
            ]);
        }
    }
}
