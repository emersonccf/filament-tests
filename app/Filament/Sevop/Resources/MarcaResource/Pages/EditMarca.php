<?php

namespace App\Filament\Sevop\Resources\MarcaResource\Pages;

use App\Filament\Sevop\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarca extends EditRecord
{
    protected static string $resource = MarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
