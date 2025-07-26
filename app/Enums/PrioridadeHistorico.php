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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BAIXA => 'success',    // Verde - baixo risco
            self::MEDIA => 'warning',    // Amarelo - atenção moderada
            self::ALTA => 'danger',      // Vermelho - alta atenção
            self::CRITICA => 'gray',     // Cinza escuro - máxima urgência
        };
    }

    /**
     * Método adicional para ícones baseados na prioridade
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::BAIXA => 'heroicon-o-arrow-down',
            self::MEDIA => 'heroicon-o-minus',
            self::ALTA => 'heroicon-o-arrow-up',
            self::CRITICA => 'heroicon-o-exclamation-triangle',
        };
    }

    /**
     * Método para descrições mais detalhadas
     */
    public function getDescription(): ?string
    {
        return match ($this) {
            self::BAIXA => 'Prioridade baixa - pode aguardar',
            self::MEDIA => 'Prioridade média - atenção moderada',
            self::ALTA => 'Prioridade alta - requer atenção urgente',
            self::CRITICA => 'Prioridade crítica - ação imediata necessária',
        };
    }

    /**
     * Método para ordenação por prioridade
     */
    public function getOrder(): int
    {
        return match ($this) {
            self::BAIXA => 1,
            self::MEDIA => 2,
            self::ALTA => 3,
            self::CRITICA => 4,
        };
    }
}
