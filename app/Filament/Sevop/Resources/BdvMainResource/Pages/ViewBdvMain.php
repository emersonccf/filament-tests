<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Filament\Sevop\Resources\BdvMainResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBdvMain extends ViewRecord
{
    protected static string $resource = BdvMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
