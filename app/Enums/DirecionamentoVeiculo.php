<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DirecionamentoVeiculo: string implements HasLabel
{
    case NORMAL = 'NORMAL';
    case FULL_TIME = 'FULL_TIME';
    case SUPERVISAO = 'SUPERVISAO';
    case GART = 'GART';
    case ESCOLTA = 'ESCOLTA';
    case SEMOB = 'SEMOB';
    case RESERVA = 'RESERVA';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NORMAL => 'Normal',
            self::FULL_TIME => 'Full Time',
            self::SUPERVISAO => 'Supervisão',
            self::GART => 'GART',
            self::ESCOLTA => 'Escolta',
            self::SEMOB => 'SEMOB',
            self::RESERVA => 'Reserva',
        };
    }
}

