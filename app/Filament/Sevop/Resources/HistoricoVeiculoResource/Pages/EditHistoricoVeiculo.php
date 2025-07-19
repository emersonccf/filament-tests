<?php

namespace App\Filament\Sevop\Resources\HistoricoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\HistoricoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoricoVeiculo extends EditRecord
{
    protected static string $resource = HistoricoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
