<?php

namespace App\Filament\Sevop\Resources\MarcaResource\Pages;

use App\Filament\Sevop\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarcas extends ListRecords
{
    protected static string $resource = MarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
