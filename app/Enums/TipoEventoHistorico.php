<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoEventoHistorico: string implements HasLabel
{
    case REVISAO = 'REVISAO';
    case MANUTENCAO_CORRETIVA = 'MANUTENCAO_CORRETIVA';
    case ACIDENTE_CHOQUE = 'ACIDENTE_CHOQUE';
    case ACIDENTE_QUEDA = 'ACIDENTE_QUEDA';
    case ACIDENTE_ATROPELAMENTO = 'ACIDENTE_ATROPELAMENTO';
    case ACIDENTE_COLISAO = 'ACIDENTE_COLISAO';
    case ACIDENTE_TOMBAMENTO = 'ACIDENTE_TOMBAMENTO';
    case ACIDENTE_ABALROAMENTO = 'ACIDENTE_ABALROAMENTO';
    case ACIDENTE_CAPOTAMENTO = 'ACIDENTE_CAPOTAMENTO';
    case ACIDENTE_ENGAVETAMENTO = 'ACIDENTE_ENGAVETAMENTO';
    case ACIDENTE_OUTROS = 'ACIDENTE_OUTROS';
    case AUSENCIA_EQUIPAMENTO = 'AUSENCIA_EQUIPAMENTO';
    case SUJEIRA = 'SUJEIRA';
    case PNEU_FURADO = 'PNEU_FURADO';
    case CHAPARIA_DANIFICADA = 'CHAPARIA_DANIFICADA';
    case PARA_CHOQUE_DANIFICADO = 'PARA_CHOQUE_DANIFICADO';
    case DEFEITO_RADIO = 'DEFEITO_RADIO';
    case DEFEITO_FAROL = 'DEFEITO_FAROL';
    case PANE = 'PANE';
    case EXTRAVIO = 'EXTRAVIO';
    case VANDALISMO = 'VANDALISMO';
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
            self::REVISAO => 'Revisão',
            self::MANUTENCAO_CORRETIVA => 'Manutenção Corretiva',
            self::ACIDENTE_CHOQUE => 'Acidente tipo Choque',
            self::ACIDENTE_QUEDA => 'Acidente tipo Queda',
            self::ACIDENTE_ATROPELAMENTO => 'Acidente tipo Atropelamento',
            self::ACIDENTE_COLISAO => 'Acidente tipo Colisão',
            self::ACIDENTE_TOMBAMENTO => 'Acidente tipo Tombamento',
            self::ACIDENTE_ABALROAMENTO => 'Acidente tipo Abalroamento',
            self::ACIDENTE_CAPOTAMENTO => 'Acidente tipo Capotamento',
            self::ACIDENTE_ENGAVETAMENTO => 'Acidente tipo Engavetamento',
            self::ACIDENTE_OUTROS => 'Acidente Outros',
            self::AUSENCIA_EQUIPAMENTO => 'Ausência de Equipamento',
            self::SUJEIRA => 'Sujeira',
            self::PNEU_FURADO => 'Pneu Furado',
            self::CHAPARIA_DANIFICADA => 'Chaparia danificada',
            self::PARA_CHOQUE_DANIFICADO => 'Para-choque danificado',
            self::DEFEITO_RADIO => 'Defeito Rádio',
            self::DEFEITO_FAROL => 'Defeito Farol',
            self::PANE => 'Pane',
            self::EXTRAVIO => 'Extravio',
            self::VANDALISMO => 'Vandalismo',
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

