<?php

namespace App\Observers;

use App\Enums\StatusEventoHistorico;
use App\Enums\StatusVeiculo;
use App\Models\HistoricoVeiculo;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Auth;

class HistoricoVeiculoObserver
{
    public function creating(HistoricoVeiculo $historicoVeiculo): void
    {
        if (Auth::check()) {
            $historicoVeiculo->cadastrado_por = Auth::id();
            $historicoVeiculo->atualizado_por = Auth::id();
        }

        // Chama o método auxiliar para gerenciar o status do veículo
        $this->updateVehicleStatus($historicoVeiculo);
    }

    public function updating(HistoricoVeiculo $historicoVeiculo): void
    {
        if (Auth::check()) {
            $historicoVeiculo->atualizado_por = Auth::id();
        }

        // Chama o método auxiliar para gerenciar o status do veículo
        $this->updateVehicleStatus($historicoVeiculo);

    }

    /**
     * Método auxiliar privado para gerenciar o status do veículo
     * com base nas características do histórico.
     *
     * @param HistoricoVeiculo $historicoVeiculo O modelo HistoricoVeiculo sendo processado.
     * @return void
     */
    private function updateVehicleStatus(HistoricoVeiculo $historicoVeiculo): void
    {
        // 1. Encontra a instância do veículo.
        // Assumimos que 'id_veiculo' é a chave primária da tabela 'veiculos'.
        // Se não for, use 'Veiculo::where('id_veiculo', $historicoVeiculo->id_veiculo)->first();'
        $veiculo = Veiculo::find($historicoVeiculo->id_veiculo);

        // Se o veículo não for encontrado, não há o que fazer.
        if (!$veiculo) {
            return;
        }

        // 2. Verifica se o histórico afeta a disponibilidade do veículo.
        if ($historicoVeiculo->afeta_disponibilidade) {

            // 3. Lógica para mudar o status do veículo para MANUTENCAO.
            if ($historicoVeiculo->status_evento === StatusEventoHistorico::PENDENTE ||
                $historicoVeiculo->status_evento === StatusEventoHistorico::EM_ANDAMENTO) {

                $veiculo->status = StatusVeiculo::MANUTENCAO;

                // 4. Lógica para mudar o status do veículo para ATIVO.
            } elseif ($historicoVeiculo->status_evento === StatusEventoHistorico::CANCELADO ||
                $historicoVeiculo->status_evento === StatusEventoHistorico::CONCLUIDO) {

                $veiculo->status = StatusVeiculo::ATIVO;
            }

            // 5. Atualiza o usuário que modificou o veículo e o timestamp.
            // (updated_at é geralmente automático, mas mantido para consistência com seu código original)
            if (Auth::check()) {
                $veiculo->atualizado_por = Auth::id();
            }
            $veiculo->updated_at = now();

            // 6. Salva as alterações no modelo do veículo.
            $veiculo->save();
        }
    }

    /**
     * Handle the HistoricoVeiculo "created" event.
     */
    public function created(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "updated" event.
     */
    public function updated(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "deleted" event.
     */
    public function deleted(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "restored" event.
     */
    public function restored(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }

    /**
     * Handle the HistoricoVeiculo "force deleted" event.
     */
    public function forceDeleted(HistoricoVeiculo $historicoVeiculo): void
    {
        //
    }
}
