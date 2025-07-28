<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NivelCombustivelEnum: string implements HasLabel
{
    case VAZIO = 'VAZIO';
    case UM_QUARTO = '1/4';
    case MEIO = '1/2';
    case TRES_QUARTOS = '3/4';
    case CHEIO = 'CHEIO';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VAZIO => 'Vazio',
            self::UM_QUARTO => '1/4',
            self::MEIO => '1/2',
            self::TRES_QUARTOS => '3/4',
            self::CHEIO => 'Cheio',
        };
    }
}
