<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LocalidadeAtivacaoTurnoVeiculo: string implements HasLabel
{
    case AREA_01 = 'AREA_01';
    case AREA_02 = 'AREA_02';
    case AREA_03 = 'AREA_03';
    case AREA_04 = 'AREA_04';
    case AREA_05 = 'AREA_05';
    case AREA_06 = 'AREA_06';
    case AREA_07 = 'AREA_07';
    case AREA_08 = 'AREA_08';
    case AREA_09 = 'AREA_09';
    case AREA_10 = 'AREA_10';
    case AREA_11 = 'AREA_11';
    case AREA_12 = 'AREA_12';
    case SUP_AREA_01 = 'SUP_AREA_01';
    case SUP_AREA_02 = 'SUP_AREA_02';
    case SUP_AREA_03 = 'SUP_AREA_03';
    case SUP_AREA_04 = 'SUP_AREA_04';
    case SUP_AREA_05 = 'SUP_AREA_05';
    case SUP_AREA_06 = 'SUP_AREA_06';
    case SUP_AREA_07 = 'SUP_AREA_07';
    case SUP_AREA_08 = 'SUP_AREA_08';
    case SUP_AREA_09 = 'SUP_AREA_09';
    case SUP_AREA_10 = 'SUP_AREA_10';
    case SUP_AREA_11 = 'SUP_AREA_11';
    case SUP_AREA_12 = 'SUP_AREA_12';
    case COORD_01 = 'COORD_01';
    case COORD_02 = 'COORD_02';
    case GART = 'GART';
    case GART_1A_GART_2B = 'GART_1A _GART_2B';
    case GART_ESCOLTA = 'GART_ESCOLTA';
    case GTRAN = 'GTRAN';
    case BLITZ_1A_BLITZ_2B = 'BLITZ_1A_BLITZ_2B';
    case BLITZ_1A = 'BLITZ_1A';
    case BLITZ_2A = 'BLITZ_2A';
    case SUP_NOT = 'SUP_NOT';
    case SEVOP_SUP_NOT = 'SEVOP_SUP_NOT';
    case GRUPOS_NOTUR = 'GRUPOS_NOTUR';
    case OUTRAS = 'OUTRAS';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AREA_01 => 'Área 01',
            self::AREA_02 => 'Área 02',
            self::AREA_03 => 'Área 03',
            self::AREA_04 => 'Área 04',
            self::AREA_05 => 'Área 05',
            self::AREA_06 => 'Área 06',
            self::AREA_07 => 'Área 07',
            self::AREA_08 => 'Área 08',
            self::AREA_09 => 'Área 09',
            self::AREA_10 => 'Área 10',
            self::AREA_11 => 'Área 11',
            self::AREA_12 => 'Área 12',
            self::SUP_AREA_01 => 'Supervisão Área 01',
            self::SUP_AREA_02 => 'Supervisão Área 02',
            self::SUP_AREA_03 => 'Supervisão Área 03',
            self::SUP_AREA_04 => 'Supervisão Área 04',
            self::SUP_AREA_05 => 'Supervisão Área 05',
            self::SUP_AREA_06 => 'Supervisão Área 06',
            self::SUP_AREA_07 => 'Supervisão Área 07',
            self::SUP_AREA_08 => 'Supervisão Área 08',
            self::SUP_AREA_09 => 'Supervisão Área 09',
            self::SUP_AREA_10 => 'Supervisão Área 10',
            self::SUP_AREA_11 => 'Supervisão Área 11',
            self::SUP_AREA_12 => 'Supervisão Área 12',
            self::COORD_01 => 'Coordenação 01',
            self::COORD_02 => 'Coordenação 02',
            self::GART => 'GART',
            self::GART_1A_GART_2B => 'GART 1A ou GART 2B',
            self::GART_ESCOLTA => 'GART Escolta Prefeito',
            self::GTRAN => 'GTRAN',
            self::BLITZ_1A_BLITZ_2B => 'BLITZ 1A ou BLITZ 2B',
            self::BLITZ_1A => 'BLITZ 1A',
            self::BLITZ_2A => 'BLITZ 2A',
            self::SUP_NOT => 'Supervisão Noturna',
            self::SEVOP_SUP_NOT => 'SEVOP e Supervisão Noturna',
            self::GRUPOS_NOTUR => 'Grupos Noturnos',
            self::OUTRAS => 'Outras Localidades',
        };
    }
}

