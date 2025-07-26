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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDENTE => 'warning',
            self::EM_ANDAMENTO => 'info',
            self::CONCLUIDO => 'success',
            self::CANCELADO => 'danger',
        };
    }

    /**
     * Método adicional para ícones (opcional)
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDENTE => 'heroicon-o-clock',
            self::EM_ANDAMENTO => 'heroicon-o-play',
            self::CONCLUIDO => 'heroicon-o-check-circle',
            self::CANCELADO => 'heroicon-o-x-circle',
        };
    }

    /**
     * Método para descrições detalhadas
     */
    public function getDescription(): ?string
    {
        return match ($this) {
            self::PENDENTE => 'Ocorrência pendente aguardando tratamento...',
            self::EM_ANDAMENTO => 'Ocorrência em andamento...',
            self::CONCLUIDO => 'Ocorrência concluída com sucesso!',
            self::CANCELADO => 'Ocorrência cancelada!',
        };
    }

}

