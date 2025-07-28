<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Filament\Sevop\Resources\BdvMainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBdvMains extends ListRecords
{
    protected static string $resource = BdvMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
