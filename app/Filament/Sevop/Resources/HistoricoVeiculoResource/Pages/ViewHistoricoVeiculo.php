<?php

namespace App\Filament\Sevop\Resources\HistoricoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\HistoricoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHistoricoVeiculo extends ViewRecord
{
    protected static string $resource = HistoricoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
