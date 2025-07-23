<?php

namespace App\Filament\Sevop\Resources\AlocacaoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\AlocacaoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAlocacaoVeiculo extends CreateRecord
{
    protected static string $resource = AlocacaoVeiculoResource::class;

    // Adicione este método para sobrescrever o comportamento de redirecionamento
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem de Marcas (index)
        return $this->getResource()::getUrl('index');
    }
}
