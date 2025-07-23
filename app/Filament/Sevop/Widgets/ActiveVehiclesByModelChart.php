<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;          // Importe o Modelo Veiculo
use App\Enums\StatusVeiculo;     // Importe o Enum StatusVeiculo
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; // Para usar DB::raw

class ActiveVehiclesByModelChart extends ChartWidget
{
    protected static ?string $heading = 'Veículos Ativos por Marca/Modelo';

//    protected static ?string $pollingInterval = '60s'; // Opcional: Atualiza a cada 60 segundos

    protected function getType(): string
    {
        return 'bar'; // Define o tipo de gráfico como barra
    }

    protected function getData(): array
    {
        // Obtém a contagem de veículos ativos por id_modelo
        // Carrega as relações modelo e marca para usar o acessor marca_modelo
        $data = Veiculo::query()
            ->where('status', StatusVeiculo::ATIVO)
            ->with('modelo.marca') // Carrega modelo e marca para o acessor
            ->select('id_modelo', DB::raw('count(*) as total'))
            ->groupBy('id_modelo')
            ->orderByDesc('total') // Opcional: ordena pelos mais numerosos
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $item) {
            // Acessa o modelo através da relação e usa o acessor marca_modelo
            if ($item->modelo) { // Garante que o modelo existe
                $labels[] = $item->modelo->marca_modelo; // Usa o acessor 'marca_modelo'
                $counts[] = $item->total;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total de Veículos Ativos',
                    'data' => $counts,
                    'backgroundColor' => '#36A2EB', // Cor das barras (azul)
                    'borderColor' => '#36A2EB',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0, // Garante que os valores no eixo Y sejam inteiros
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Oculta a legenda se houver apenas um dataset
                ],
            ],
            'indexAxis' => 'y', // Opcional: para um gráfico de barras horizontal, troque para 'x' para vertical
        ];
    }
}
