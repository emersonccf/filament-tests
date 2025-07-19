<?php

namespace App\Filament\Sevop\Pages;

use Filament\Pages\Dashboard as BaseDashboard; // Importa a classe Dashboard base do Filament

class Dashboard extends BaseDashboard
{
    // Este é o Dashboard padrão do Filament.
     protected static ?string $title = 'Painel: Controle de Frota';
     protected static ?string $navigationIcon = 'heroicon-o-truck';

    // Adicionar widgets específicos a este dashboard:
    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         \App\Filament\Sevop\Widgets\OverviewWidget::class,
    //         // Outros widgets aqui
    //     ];
    // }

    // Para uma view Blade completamente personalizada para o Dashboard:
    // Precisaria criar o arquivo resources/views/filament/sevop/pages/dashboard.blade.php)
    // protected static string $view = 'filament.sevop.pages.dashboard';
}
