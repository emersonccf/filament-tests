<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoRegistroStatusEnum: string implements HasLabel
{
    case SAIDA = 'Saida';
    case CHEGADA = 'Chegada';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SAIDA => 'Registro de Saída',
            self::CHEGADA => 'Registro de Chegada',
        };
    }
}
