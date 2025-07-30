<?php

namespace App\Filament\Sevop\Resources\VeiculoResource\Pages;

use App\Filament\Sevop\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVeiculo extends ViewRecord
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
