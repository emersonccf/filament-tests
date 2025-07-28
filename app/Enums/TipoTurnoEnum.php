<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoTurnoEnum: string implements HasLabel
{
    case MATUTINO = 'Matutino';
    case VESPERTINO = 'Vespertino';
    case DIURNO = 'Diurno'; // Se um motorista usar o veículo o dia todo
    case NOTURNO = 'Noturno';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MATUTINO => 'Matutino',
            self::VESPERTINO => 'Vespertino',
            self::DIURNO => 'Diurno (Dia Inteiro)',
            self::NOTURNO => 'Noturno',
        };
    }
}
