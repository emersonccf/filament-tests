<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;              // Importe o Modelo Veiculo
use App\Enums\StatusVeiculo;         // Importe o Enum StatusVeiculo
use App\Enums\LocalAtivacaoVeiculo;   // Importe o Enum LocalAtivacaoVeiculo
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB; // Para usar DB::raw

class ActiveVehiclesByLocationTable extends Widget
{
    // Define a view Blade que este widget irá renderizar
    protected static string $view = 'filament.sevop.widgets.active-vehicles-by-location-table';

    // Define o título que aparecerá no cabeçalho do widget
    protected static ?string $heading = 'Total de Veículos Ativos por Local de Ativação (Detalhes)';

    // Opcional: Faz o widget recarregar seus dados periodicamente (a cada 60 segundos)
//    protected static ?string $pollingInterval = '60s';

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

    /**
     * Método para obter e processar os dados dos veículos para a tabela.
     * @return array
     */
    protected function getVehiclesDataForTable(): array
    {
        // 1. Mapear todos os possíveis locais de ativação do Enum para seus rótulos amigáveis.
        // Isso garante que todos os locais apareçam na tabela, mesmo que não tenham veículos ativos.
        $allLocations = collect(LocalAtivacaoVeiculo::cases())->mapWithKeys(function($enum) {
            return [$enum->value => $enum->getLabel()];
        })->toArray();

        // 2. Buscar a contagem de veículos ativos por local de ativação e número de rodas no banco de dados.
        $rawData = Veiculo::query()
//            ->where('status', StatusVeiculo::ATIVO)
            ->where('data_devolucao', null) // para possibilitar contabilizar os veículos reservas que estão inativos
            // Faz um JOIN com a tabela de modelos para acessar a coluna 'numero_rodas'
            ->join('modelos', 'veiculos.id_modelo', '=', 'modelos.id_modelo')
            ->select(
                'veiculos.local_ativacao',
                // Soma os veículos com 4 ou mais rodas (carro, caminhão, van, etc.)
                DB::raw('SUM(CASE WHEN modelos.numero_rodas >= 4 THEN 1 ELSE 0 END) as four_wheelers'),
                // Soma os veículos com 2 rodas (moto, motoneta, etc.)
                DB::raw('SUM(CASE WHEN modelos.numero_rodas = 2 THEN 1 ELSE 0 END) as two_wheelers')
            )
            ->groupBy('veiculos.local_ativacao')
            ->get();

        // 3. Inicializar arrays para armazenar os dados finais da tabela e os totais gerais.
        $processedData = [];
        $grandTotalFourWheelers = 0;
        $grandTotalTwoWheelers = 0;

        // 4. Processar os dados brutos, garantindo que todos os locais de ativação sejam incluídos.
        foreach ($allLocations as $enumValue => $friendlyLabel) {
            // Tenta encontrar a linha correspondente nos dados brutos do banco
            $foundRow = $rawData->firstWhere('local_ativacao', $enumValue);

            // Obtém as contagens ou 0 se o local não tiver veículos ativos no momento
            $fourWheelersCount = $foundRow ? $foundRow->four_wheelers : 0;
            $twoWheelersCount = $foundRow ? $foundRow->two_wheelers : 0;
            $totalCount = $fourWheelersCount + $twoWheelersCount;

            // Adiciona a linha processada aos dados da tabela
            $processedData[] = [
                'location_label' => $friendlyLabel, // Rótulo amigável para exibição
                'four_wheelers' => $fourWheelersCount,
                'two_wheelers' => $twoWheelersCount,
                'total' => $totalCount,
                'is_total_row' => false, // Marca esta como uma linha de dados normal
            ];

            // Acumula os totais gerais
            $grandTotalFourWheelers += $fourWheelersCount;
            $grandTotalTwoWheelers += $twoWheelersCount;
        }

        // 5. Adicionar a linha de "Total Geral" no final da tabela.
        $processedData[] = [
            'location_label' => 'Total Geral',
            'four_wheelers' => $grandTotalFourWheelers,
            'two_wheelers' => $grandTotalTwoWheelers,
            'total' => $grandTotalFourWheelers + $grandTotalTwoWheelers,
            'is_total_row' => true, // Marca esta como a linha de total para estilização
        ];

        return $processedData;
    }
}
