<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoEventoHistorico: string implements HasLabel
{
    case MANUTENCAO_PREVENTIVA = 'MANUTENCAO_PREVENTIVA';
    case MANUTENCAO_CORRETIVA = 'MANUTENCAO_CORRETIVA';
    case REVISAO = 'REVISAO';
    case ACIDENTE = 'ACIDENTE';
    case AUSENCIA_EQUIPAMENTO = 'AUSENCIA_EQUIPAMENTO';
    case SUJEIRA = 'SUJEIRA';
    case PNEU_FURADO = 'PNEU_FURADO';
    case AMASSADO_CHAPARIA = 'AMASSADO_CHAPARIA';
    case AMASSADO_PARA_CHOQUE = 'AMASSADO_PARA_CHOQUE';
    case DEFEITO_RADIO = 'DEFEITO_RADIO';
    case DEFEITO_FAROL = 'DEFEITO_FAROL';
    case DEFEITO_LANTERNA = 'DEFEITO_LANTERNA';
    case FALHA_GIROFLEX = 'FALHA_GIROFLEX';
    case TROCA_OLEO = 'TROCA_OLEO';
    case TROCA_FILTRO = 'TROCA_FILTRO';
    case ALINHAMENTO = 'ALINHAMENTO';
    case BALANCEAMENTO = 'BALANCEAMENTO';
    case TROCA_PNEU = 'TROCA_PNEU';
    case LIMPEZA = 'LIMPEZA';
    case ABASTECIMENTO = 'ABASTECIMENTO';
    case INSPECAO = 'INSPECAO';
    case OUTROS = 'OUTROS';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MANUTENCAO_PREVENTIVA => 'Manutenção Preventiva',
            self::MANUTENCAO_CORRETIVA => 'Manutenção Corretiva',
            self::REVISAO => 'Revisão',
            self::ACIDENTE => 'Acidente',
            self::AUSENCIA_EQUIPAMENTO => 'Ausência de Equipamento',
            self::SUJEIRA => 'Sujeira',
            self::PNEU_FURADO => 'Pneu Furado',
            self::AMASSADO_CHAPARIA => 'Amassado Chapa',
            self::AMASSADO_PARA_CHOQUE => 'Amassado Para-choque',
            self::DEFEITO_RADIO => 'Defeito Rádio',
            self::DEFEITO_FAROL => 'Defeito Farol',
            self::DEFEITO_LANTERNA => 'Defeito Lanterna',
            self::FALHA_GIROFLEX => 'Falha Giroflex',
            self::TROCA_OLEO => 'Troca Óleo',
            self::TROCA_FILTRO => 'Troca Filtro',
            self::ALINHAMENTO => 'Alinhamento',
            self::BALANCEAMENTO => 'Balanceamento',
            self::TROCA_PNEU => 'Troca Pneu',
            self::LIMPEZA => 'Limpeza',
            self::ABASTECIMENTO => 'Abastecimento',
            self::INSPECAO => 'Inspeção',
            self::OUTROS => 'Outros',
        };
    }
}

