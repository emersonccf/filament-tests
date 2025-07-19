<?php

namespace App\Filament\Sevop\Resources\ModeloResource\Pages;

use App\Filament\Sevop\Resources\ModeloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModelos extends ListRecords
{
    protected static string $resource = ModeloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
