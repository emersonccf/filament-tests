<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;          // Importe o Modelo Veiculo
use App\Enums\StatusVeiculo;     // Importe o Enum StatusVeiculo
use Filament\Widgets\Widget;

class MaintenanceVehiclesTable extends Widget
{
    // Define a view Blade que este widget irá renderizar
    protected static string $view = 'filament.sevop.widgets.maintenance-vehicles-table';

    // Define o título que aparecerá no cabeçalho do widget
    protected static ?string $heading = 'Veículos Ativos em Manutenção';

    // Opcional: Faz o widget recarregar seus dados periodicamente (a cada 30 segundos)
    protected static ?string $pollingInterval = '30s';

    // Propriedades públicas para armazenar os dados e o total, acessíveis na view Blade
    public array $maintenanceVehicles = [];
    public int $totalMaintenanceVehicles = 0;

    /**
     * O método mount() é chamado quando o componente Livewire (que o widget é) é inicializado.
     * Usamos ele para popular os dados antes que a view seja renderizada.
     */
    public function mount(): void
    {
        $this->loadMaintenanceVehicles();
    }

    /**
     * Método para carregar os veículos em manutenção.
     * @return void
     */
    protected function loadMaintenanceVehicles(): void
    {
        // Busca todos os veículos com status de MANUTENCAO.
        // Carrega ansiosamente os relacionamentos 'modelo' e, através de 'modelo', a 'marca'.
        $vehicles = Veiculo::query()
            ->where('status', StatusVeiculo::MANUTENCAO)
            ->with(['modelo.marca']) // Carrega Marca através do relacionamento de Modelo
            ->get();

        $processedVehicles = [];

        foreach ($vehicles as $veiculo) {
            $marcaModelo = 'N/A';
            if ($veiculo->modelo) {
                // Monta a string 'Marca / Modelo'
                $marcaNome = $veiculo->modelo->marca ? $veiculo->modelo->marca->nome_marca : 'N/A';
                $modeloNome = $veiculo->modelo->nome_modelo ? $veiculo->modelo->nome_modelo : 'N/A';
                $marcaModelo = $modeloNome;
//                $marcaModelo = $marcaNome . ' / ' . $modeloNome;
            }

            $processedVehicles[] = [
                'id_veiculo' => $veiculo->id_veiculo, // <-- ADICIONADO: O ID do veículo para construir a URL
                'placa' => $veiculo->placa,
                'marca_modelo' => $marcaModelo,
                'prefixo_veiculo' => $veiculo->prefixo_veiculo,
                'local_ativacao' => $veiculo->local_ativacao->getLabel(), // Assume que local_ativacao é um Enum
            ];
        }

        $this->maintenanceVehicles = $processedVehicles;
        $this->totalMaintenanceVehicles = count($processedVehicles);
    }
}
