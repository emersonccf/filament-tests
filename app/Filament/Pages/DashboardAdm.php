<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard; // Importa a classe Dashboard base do Filament

class DashboardAdm extends BaseDashboard
{
    // Este é o Dashboard padrão do Filament.
     protected static ?string $title = 'Painel: Administração de Usuários do Sistema';
     protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
     // https://heroicons.com/outline

}
