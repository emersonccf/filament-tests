<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LocalAtivacaoVeiculo: string implements HasLabel
{
    case GTRAN = 'GTRAN';
    case GESIN = 'GESIN';
    case RODOVIARIA = 'RODOVIARIA';
    case PERIPERI = 'PERIPERI';
    case PARIPE = 'PARIPE';
    case FTC = 'FTC';
    case ORLANDO_GOMES = 'ORLANDO_GOMES';
    case LIMPURB = 'LIMPURB';
    case PREFEITURA = 'PREFEITURA';
    case RESERVA = 'RESERVA';
    case SEMOB = 'SEMOB';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GTRAN => 'GTRAN',
            self::GESIN => 'GESIN',
            self::RODOVIARIA => 'Rodoviária',
            self::PERIPERI => 'Periperi',
            self::PARIPE => 'Paripe',
            self::FTC => 'FTC',
            self::ORLANDO_GOMES => 'Orlando Gomes',
            self::LIMPURB => 'Limpurb',
            self::PREFEITURA => 'Prefeitura',
            self::RESERVA => 'Reserva',
            self::SEMOB => 'SEMOB',
        };
    }
}

