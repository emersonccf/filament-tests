<?php

namespace App\Filament\Sevop\Resources\AlocacaoVeiculoResource\Pages;

use App\Filament\Sevop\Resources\AlocacaoVeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlocacaoVeiculos extends ListRecords
{
    protected static string $resource = AlocacaoVeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
