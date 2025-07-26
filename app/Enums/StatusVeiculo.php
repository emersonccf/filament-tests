<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum StatusVeiculo: string implements HasLabel, HasColor
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ATIVO => 'success',      // Verde - operacional
            self::INATIVO => 'gray',       // Cinza - fora de operação
            self::MANUTENCAO => 'warning', // Amarelo - em manutenção
        };
    }

    /**
     * Método para ícones baseados no status do veículo
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::ATIVO => 'heroicon-o-check-circle',
            self::INATIVO => 'heroicon-o-x-circle',
            self::MANUTENCAO => 'heroicon-o-wrench-screwdriver',
        };
    }

    /**
     * Método para descrições detalhadas
     */
    public function getDescription(): ?string
    {
        return match ($this) {
            self::ATIVO => 'Veículo ativo e disponível para uso',
            self::INATIVO => 'Veículo inativo e indisponível',
            self::MANUTENCAO => 'Veículo em manutenção - temporariamente indisponível',
        };
    }

    /**
     * Verifica se o veículo está disponível para uso
     */
    public function isAvailable(): bool
    {
        return $this === self::ATIVO;
    }

    /**
     * Verifica se o veículo está operacional (ativo ou em manutenção)
     */
    public function isOperational(): bool
    {
        return in_array($this, [self::ATIVO, self::MANUTENCAO]);
    }
}
