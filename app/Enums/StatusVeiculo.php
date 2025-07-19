<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusVeiculo: string implements HasLabel
{
    case ATIVO = 'ATIVO';
    case INATIVO = 'INATIVO';
    case MANUTENCAO = 'MANUTENCAO';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ATIVO => 'Ativo',
            self::INATIVO => 'Inativo',
            self::MANUTENCAO => 'Manutenção',
        };
    }
}
