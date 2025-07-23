<?php

namespace App\Filament\Sevop\Resources\HistoricoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\HistoricoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHistoricoVeiculo extends CreateRecord
{
    protected static string $resource = HistoricoVeiculoResource::class;

    // Adicione este método para sobrescrever o comportamento de redirecionamento
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem de Marcas (index)
        return $this->getResource()::getUrl('index');
    }
}
