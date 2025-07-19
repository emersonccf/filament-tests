<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoriaVeiculo: string implements HasLabel
{
    case HATCH = 'HATCH';
    case SEDAN = 'SEDAN';
    case SUV = 'SUV';
    case PICKUP = 'PICKUP';
    case VAN = 'VAN';
    case CAMINHAO = 'CAMINHAO';
    case ONIBUS = 'ONIBUS';
    case MICRO_ONIBUS = 'MICRO_ONIBUS';
    case MOTOCICLETA = 'MOTOCICLETA';
    case MOTONETA = 'MOTONETA';
    case CICLOMOTOR = 'CICLOMOTOR';
    case BICICLETA = 'BICICLETA';
    case TRICICLO = 'TRICICLO';
    case QUADRICICLO = 'QUADRICICLO';
    case TRATOR = 'TRATOR';
    case MAQUINA_AGRICOLA = 'MAQUINA_AGRICOLA';
    case OUTROS = 'OUTROS';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HATCH => 'Hatch',
            self::SEDAN => 'Sedan',
            self::SUV => 'SUV',
            self::PICKUP => 'Pickup',
            self::VAN => 'Van',
            self::CAMINHAO => 'Caminhão',
            self::ONIBUS => 'Ônibus',
            self::MICRO_ONIBUS => 'Micro-ônibus',
            self::MOTOCICLETA => 'Motocicleta',
            self::MOTONETA => 'Motoneta',
            self::CICLOMOTOR => 'Ciclomotor',
            self::BICICLETA => 'Bicicleta',
            self::TRICICLO => 'Triciclo',
            self::QUADRICICLO => 'Quadriciclo',
            self::TRATOR => 'Trator',
            self::MAQUINA_AGRICOLA => 'Máquina Agrícola',
            self::OUTROS => 'Outros',
        };
    }
}

