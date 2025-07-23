<?php

namespace App\Filament\Sevop\Resources\UnidadeResource\Pages;

use App\Filament\Sevop\Resources\UnidadeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnidade extends EditRecord
{
    protected static string $resource = UnidadeResource::class;

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
