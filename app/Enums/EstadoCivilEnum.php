<?php

namespace App\Enums;

enum EstadoCivilEnum: int
{
    case SOLTEIRO = 1;
    case CASADO = 2;
    case UNIAO_ESTAVEL = 3;
    case SEPARADO = 4;
    case DIVORCIADO = 5;
    case VIUVO = 6;
}
