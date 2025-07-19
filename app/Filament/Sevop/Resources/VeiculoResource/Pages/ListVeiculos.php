<?php

namespace App\Filament\Sevop\Resources\VeiculoResource\Pages;

use App\Filament\Sevop\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVeiculos extends ListRecords
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
