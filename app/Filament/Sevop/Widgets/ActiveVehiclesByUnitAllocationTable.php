<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;              // Importe o Modelo Veiculo
use App\Models\Unidade;
use App\Models\AlocacaoVeiculo;      // Importe o Modelo AlocacaoVeiculo
use App\Enums\StatusVeiculo;         // Importe o Enum StatusVeiculo
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ActiveVehiclesByUnitAllocationTable extends Widget
{
    // Define a view Blade que este widget irá renderizar
    protected static string $view = 'filament.sevop.widgets.active-vehicles-by-unit-allocation-table';

    // Define o título que aparecerá no cabeçalho do widget
    protected static ?string $heading = 'Veículos Ativos por Unidade de Alocação';

    // Opcional: Faz o widget recarregar seus dados periodicamente (a cada 60 segundos)
    protected static ?string $pollingInterval = '60s';

    // Propriedade pública para armazenar os dados processados, que serão acessíveis na view Blade
    public array $tableData = [];

    /**
     * O método mount() é chamado quando o componente Livewire (que o widget é) é inicializado.
     * Usamos ele para popular os dados antes que a view seja renderizada.
     */
    public function mount(): void
    {
        $this->tableData = $this->getVehiclesDataForTable();
    }

    protected function getVehiclesDataForTable(): array
    {
        // 1. Obter todos os veículos ativos.
        $activeVehicles = Veiculo::query()
            ->where('status', StatusVeiculo::ATIVO)
            ->with(['modelo', 'alocacoes' => function ($query) {
                // Carrega ansiosamente 'unidade' dentro da relação 'alocacoes'
                $query->orderBy('data_inicio', 'desc')
                    ->orderBy('id_alocacao', 'desc') // Mantém o desempate
                    ->with('unidade'); // <--- AQUI: Carrega o relacionamento com Unidade
            }])
            ->get();

        // 2. Mapear cada veículo ativo para sua unidade de alocação mais recente e tipo de roda.
        $unitCounts = [];

        foreach ($activeVehicles as $veiculo) {
            $unitName = 'Não Alocado'; // Categoria padrão para veículos sem alocação
            $modelo = $veiculo->modelo;

            // Se o veículo tiver alocações, pega a unidade da alocação mais recente
            if ($veiculo->alocacoes->isNotEmpty()) {
                $latestAllocation = $veiculo->alocacoes->first();

                // Verifica se a relação 'unidade' foi carregada e se a unidade existe
                if ($latestAllocation->unidade) {
                    // Usa o nome_unidade da unidade relacionada
                    $unitName = $latestAllocation->unidade->nome_unidade; // <--- AQUI: Pega o nome da unidade
                } else {
                    // Fallback se a unidade não for encontrada (ex: id_unidade inválido na alocação)
                    $unitName = 'Unidade Desconhecida (ID: ' . $latestAllocation->id_unidade . ')';
                }
            }

            // Inicializa as contagens para esta unidade se ainda não existirem
            if (!isset($unitCounts[$unitName])) { // $unitName agora é uma string válida
                $unitCounts[$unitName] = [
                    'four_wheelers' => 0,
                    'two_wheelers' => 0,
                    'total' => 0,
                ];
            }

            // Incrementa as contagens baseadas no número de rodas do modelo
            if ($modelo) {
                if ($modelo->numero_rodas >= 4) {
                    $unitCounts[$unitName]['four_wheelers']++;
                } elseif ($modelo->numero_rodas == 2) {
                    $unitCounts[$unitName]['two_wheelers']++;
                }
                $unitCounts[$unitName]['total']++;
            }
        }

        // 3. Preparar os dados finais para a tabela, incluindo totalizações.
        $processedData = [];
        $grandTotalFourWheelers = 0;
        $grandTotalTwoWheelers = 0;
        $grandTotalOverall = 0;

        // Ordena as unidades alfabeticamente, mas mantém 'Não Alocado' no final
        uksort($unitCounts, function($a, $b) {
            if ($a === 'Não Alocado') return 1;
            if ($b === 'Não Alocado') return -1;
            return $a <=> $b;
        });

        foreach ($unitCounts as $unitName => $counts) {
            $processedData[] = [
                'unit_name' => $unitName, // Nome da unidade
                'four_wheelers' => $counts['four_wheelers'],
                'two_wheelers' => $counts['two_wheelers'],
                'total' => $counts['total'],
                'is_total_row' => false,
            ];
            $grandTotalFourWheelers += $counts['four_wheelers'];
            $grandTotalTwoWheelers += $counts['two_wheelers'];
            $grandTotalOverall += $counts['total'];
        }

        // 4. Adicionar a linha de "Total Geral" no final da tabela.
        $processedData[] = [
            'unit_name' => 'Total Geral',
            'four_wheelers' => $grandTotalFourWheelers,
            'two_wheelers' => $grandTotalTwoWheelers,
            'total' => $grandTotalOverall,
            'is_total_row' => true,
        ];

        return $processedData;
    }
}
