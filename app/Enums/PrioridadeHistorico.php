<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PrioridadeHistorico: string implements HasLabel
{
    case BAIXA = 'BAIXA';
    case MEDIA = 'MEDIA';
    case ALTA = 'ALTA';
    case CRITICA = 'CRITICA';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BAIXA => 'Baixa',
            self::MEDIA => 'Média',
            self::ALTA => 'Alta',
            self::CRITICA => 'Crítica',
        };
    }
}
