<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CombustivelVeiculo: string implements HasLabel
{
    case GASOLINA = 'GASOLINA';
    case ETANOL = 'ETANOL';
    case DIESEL = 'DIESEL';
    case FLEX = 'FLEX';
    case GNV = 'GNV';
    case ELETRICO = 'ELETRICO';
    case HUMANO = 'HUMANO';
    case NAO_APLICAVEL = 'NAO_APLICAVEL';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GASOLINA => 'Gasolina',
            self::ETANOL => 'Etanol',
            self::DIESEL => 'Diesel',
            self::FLEX => 'Flex',
            self::GNV => 'GNV',
            self::ELETRICO => 'Elétrico',
            self::HUMANO => 'Humano',
            self::NAO_APLICAVEL => 'Não Aplicável',
        };
    }
}
