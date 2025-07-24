<?php

namespace App\Filament\Widgets;

use App\Enums\StatusVeiculo; // Importar o Enum StatusVeiculo
use App\Enums\TipoEventoHistorico; // Importar o Enum TipoEventoHistorico
use App\Enums\StatusEventoHistorico; // Importar o Enum StatusEventoHistorico
use App\Models\HistoricoVeiculo; // Importar o Model HistoricoVeiculo
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB; // Para usar o Query Builder

class FleetEventCrossTabulation extends Widget
{
    // Define a view Blade que este widget usará para renderizar o conteúdo.
    protected static string $view = 'filament.sevop.widgets.fleet-event-cross-tabulation';

    // Define o título que aparecerá no cabeçalho do widget
    protected static ?string $heading = 'Ocorrências de Eventos da Frota (Veículos Ativos)';

    // Opcional: Faz o widget recarregar seus dados periodicamente (a cada 30 segundos)
    protected static ?string $pollingInterval = '30s';

    // Propriedades públicas para armazenar os dados da tabela cruzada
    public array $eventTypes = [];
    public array $eventStatuses = [];
    public array $pivotTable = [];
    public array $rowTotals = [];
    public array $columnTotals = [];
    public int $grandTotal = 0;

    /**
     * O método mount() é chamado quando o componente Livewire (que o widget é) é inicializado.
     * Usamos ele para popular os dados antes que a view seja renderizada.
     */
    public function mount(): void
    {
        $this->loadCrossTabData();
    }

    /**
     * Método para carregar e processar os dados para a tabela cruzada.
     * @return void
     */
    protected function loadCrossTabData(): void
    {
        // 1. Definir os cabeçalhos das linhas (tipos de evento) e colunas (status do evento)
        $this->eventTypes = collect(TipoEventoHistorico::cases())->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])->toArray();
        $this->eventStatuses = collect(StatusEventoHistorico::cases())->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])->toArray();

        // 2. Consultar os dados brutos do histórico
        $rawData = HistoricoVeiculo::query()
            // Juntar com a tabela de veículos para filtrar apenas os ativos
            ->join('veiculos', 'historico_veiculos.id_veiculo', '=', 'veiculos.id_veiculo')
            ->where('veiculos.status', StatusVeiculo::ATIVO->value) // Filtrar por veículos ativos
            // Selecionar os tipos de evento, status e a contagem de ocorrências
            ->select(
                'historico_veiculos.tipo_evento',
                'historico_veiculos.status_evento',
                DB::raw('COUNT(*) as count')
            )
            // Agrupar pelos tipos de evento e status para obter a contagem por combinação
            ->groupBy('historico_veiculos.tipo_evento', 'historico_veiculos.status_evento')
            ->get();

        // 3. Inicializar a matriz da tabela cruzada e as totalizações
        $pivotTable = [];
        $rowTotals = [];
        $columnTotals = [];
        $grandTotal = 0;

        // Preencher a matriz com zeros e inicializar os totais de linha
        foreach ($this->eventTypes as $value => $label) {
            $pivotTable[$value] = [];
            $rowTotals[$value] = 0;
            foreach ($this->eventStatuses as $statusValue => $statusLabel) {
                $pivotTable[$value][$statusValue] = 0;
            }
        }

        // Inicializar os totais de coluna
        foreach ($this->eventStatuses as $statusValue => $statusLabel) {
            $columnTotals[$statusValue] = 0;
        }

        // 4. Popular a matriz com os dados da consulta e calcular os totais
        foreach ($rawData as $row) {
            // CORREÇÃO: Acesse a propriedade 'value' do objeto Enum
            $tipoEvento = $row->tipo_evento->value;
            $statusEvento = $row->status_evento->value;
            $count = $row->count;

            // Garantir que a chave existe antes de atribuir
            if (isset($pivotTable[$tipoEvento]) && isset($pivotTable[$tipoEvento][$statusEvento])) {
                $pivotTable[$tipoEvento][$statusEvento] = $count;
                $rowTotals[$tipoEvento] += $count;
                $columnTotals[$statusEvento] += $count;
                $grandTotal += $count;
            }
        }

        // Atribuir os resultados às propriedades públicas do widget
        $this->pivotTable = $pivotTable;
        $this->rowTotals = $rowTotals;
        $this->columnTotals = $columnTotals;
        $this->grandTotal = $grandTotal;
    }
}
