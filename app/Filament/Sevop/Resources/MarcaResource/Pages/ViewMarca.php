<?php

namespace App\Filament\Sevop\Resources\MarcaResource\Pages;

use App\Filament\Sevop\Resources\MarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMarca extends ViewRecord
{
    protected static string $resource = MarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
