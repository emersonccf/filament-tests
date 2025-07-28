<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Filament\Sevop\Resources\BdvMainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBdvMain extends EditRecord
{
    protected static string $resource = BdvMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
