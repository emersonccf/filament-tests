<?php

namespace App\Filament\Sevop\Resources\VeiculoResource\Pages;

use App\Filament\Sevop\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVeiculo extends EditRecord
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Adicione este método para sobrescrever o comportamento de redirecionamento
    protected function getRedirectUrl(): string
    {
        // Redireciona para a página de listagem de Marcas (index)
        return $this->getResource()::getUrl('index');
    }
}
