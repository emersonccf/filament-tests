<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusEventoHistorico: string implements HasLabel
{
    case PENDENTE = 'PENDENTE';
    case EM_ANDAMENTO = 'EM_ANDAMENTO';
    case CONCLUIDO = 'CONCLUIDO';
    case CANCELADO = 'CANCELADO';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::EM_ANDAMENTO => 'Em Andamento',
            self::CONCLUIDO => 'Concluído',
            self::CANCELADO => 'Cancelado',
        };
    }
}

