<?php

namespace App\Filament\Sevop\Resources\MarcaResource\Pages;

use App\Filament\Sevop\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMarca extends CreateRecord
{
    protected static string $resource = MarcaResource::class;

    // Adicione este método para sobrescrever o comportamento de redirecionamento
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem de Marcas (index)
        return $this->getResource()::getUrl('index');
    }
}
