<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;          // Importe o Modelo Veiculo
use App\Enums\StatusVeiculo;     // Importe o Enum StatusVeiculo
use App\Enums\LocalAtivacaoVeiculo; // Importe o Enum LocalAtivacaoVeiculo
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; // Para usar DB::raw

class ActiveVehiclesByLocationChart extends ChartWidget
{
    protected static ?string $heading = 'Veículos Ativos por Local de Ativação';

    protected static ?string $pollingInterval = '60s'; // Opcional: Atualiza a cada 60 segundos

    protected function getType(): string
    {
        return 'bar'; // Define o tipo de gráfico como barra
    }

    protected function getData(): array
    {
        // Mapeia todos os possíveis locais de ativação do Enum para seus rótulos amigáveis
        $allLocations = collect(LocalAtivacaoVeiculo::cases())->mapWithKeys(function($enum) {
            return [$enum->value => $enum->getLabel()];
        })->toArray();

        // Inicializa os arrays de dados com zeros para todos os locais, garantindo que apareçam no gráfico
        $fourWheelersData = array_fill_keys(array_keys($allLocations), 0);
        $twoWheelersData = array_fill_keys(array_keys($allLocations), 0);

        // Busca a contagem de veículos ativos por local de ativação e número de rodas
        $rawData = Veiculo::query()
            ->where('status', StatusVeiculo::ATIVO)
            ->join('modelos', 'veiculos.id_modelo', '=', 'modelos.id_modelo') // Join para acessar numero_rodas
            ->select(
                'veiculos.local_ativacao',
                DB::raw('SUM(CASE WHEN modelos.numero_rodas >= 4 THEN 1 ELSE 0 END) as four_wheelers'),
                DB::raw('SUM(CASE WHEN modelos.numero_rodas = 2 THEN 1 ELSE 0 END) as two_wheelers')
            )
            ->groupBy('veiculos.local_ativacao')
            ->get();

        // Preenche os arrays de dados com os valores obtidos do banco
        foreach ($rawData as $row) {
            // Acessa o valor string do Enum para usar como chave
            $locationKey = $row->local_ativacao->value; // <-- CORREÇÃO PRINCIPAL AQUI

            if (array_key_exists($locationKey, $fourWheelersData)) {
                $fourWheelersData[$locationKey] = $row->four_wheelers;
                $twoWheelersData[$locationKey] = $row->two_wheelers;
            }
        }

        // Prepara os labels e datasets para o Chart.js
        $labels = array_values($allLocations); // Usa os rótulos amigáveis para o eixo
        $fourWheelersData = array_values($fourWheelersData);
        $twoWheelersData = array_values($twoWheelersData);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => '4 Rodas',
                    'data' => $fourWheelersData,
                    'backgroundColor' => '#36A2EB', // Cor azul para 4 rodas
                    'borderColor' => '#36A2EB',
                    'borderWidth' => 1,
                ],
                [
                    'label' => '2 Rodas',
                    'data' => $twoWheelersData,
                    'backgroundColor' => '#FFCD56', // Cor amarela para 2 rodas
                    'borderColor' => '#FFCD56',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Define o gráfico de barras como horizontal
            'scales' => [
                'x' => [ // Eixo X (valores) para barras horizontais
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0, // Garante que os valores no eixo X sejam inteiros
                    ],
                    'stacked' => true, // Habilita o empilhamento para o eixo X
                ],
                'y' => [ // Eixo Y (categorias) para barras horizontais
                    'stacked' => true, // Habilita o empilhamento para o eixo Y
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true, // Mostra a legenda para diferenciar 2 e 4 rodas
                    'position' => 'right', // Posição da legenda
                ],
                'datalabels' => [ // Configurações para o plugin datalabels
                    'display' => true,
                    'color' => '#FFFFFF', // Cor do texto da label (branco para contraste)
                    'font' => [
                        'weight' => 'bold',
                        'size' => 12,
                    ],
                    // Formata a label: exibe o valor apenas se for maior que 0
                    'formatter' => "function(value, context) { return value > 0 ? value : ''; }",
                ],
            ],
        ];
    }
}
