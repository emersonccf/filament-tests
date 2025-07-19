<?php

namespace App\Filament\Sevop\Resources\HistoricoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\HistoricoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoricoVeiculos extends ListRecords
{
    protected static string $resource = HistoricoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
