<?php

namespace App\Filament\Sevop\Widgets;

use App\Models\Veiculo;          // Importe o Modelo Veiculo
use App\Enums\StatusVeiculo;     // Importe o Enum StatusVeiculo
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat; // Importe o Stat

class ActiveVehiclesOverview extends BaseWidget
{
//    protected static ?string $pollingInterval = '120s'; // Opcional: Atualiza a cada 30 segundos

    protected function getStats(): array
    {
        // Obtém todos os veículos ativos, carregando a relação 'modelo'
        // Isso é importante para acessar 'modelo.numero_rodas'
        $activeVehicles = Veiculo::query()
            ->where('status', StatusVeiculo::ATIVO)
            ->with('modelo') // Carrega a relação 'modelo' para cada veículo
            ->get();

        $totalActive = $activeVehicles->count();

        // Filtra os veículos para contar os de 4 rodas ou mais
        $fourWheelers = $activeVehicles->filter(function ($veiculo) {
            // Certifique-se de que o modelo existe e tem o atributo 'numero_rodas'
            return $veiculo->modelo && $veiculo->modelo->numero_rodas >= 4;
        })->count();

        // Filtra os veículos para contar os de 2 rodas
        $twoWheelers = $activeVehicles->filter(function ($veiculo) {
            // Certifique-se de que o modelo existe e tem o atributo 'numero_rodas'
            return $veiculo->modelo && $veiculo->modelo->numero_rodas == 2;
        })->count();

        return [
            Stat::make('Total de Veículos Ativos', $totalActive)
                ->description('Veículos prontos para uso')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),
            Stat::make('Veículos 4 Rodas (Ativos)', $fourWheelers)
                ->description('Carros, vans etc.')
                ->descriptionIcon('heroicon-s-truck')
//                ->descriptionIcon('heroicon-s-car')
                ->color('info'),
            Stat::make('Veículos 2 Rodas (Ativos)', $twoWheelers)
                ->description('Motos, etc.')
                ->descriptionIcon('heroicon-s-bolt')
//                ->descriptionIcon('heroicon-s-moped')
                ->color('warning'),
        ];
    }
}
