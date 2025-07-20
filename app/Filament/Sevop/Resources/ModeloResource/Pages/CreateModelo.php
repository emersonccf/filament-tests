<?php

namespace App\Filament\Sevop\Resources\ModeloResource\Pages;

use App\Filament\Sevop\Resources\ModeloResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateModelo extends CreateRecord
{
    protected static string $resource = ModeloResource::class;

    // Adicione este método para sobrescrever o comportamento de redirecionamento
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem de Marcas (index)
        return $this->getResource()::getUrl('index');
    }
}
