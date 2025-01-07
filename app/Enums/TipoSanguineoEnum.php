<?php

namespace App\Enums;

enum TipoSanguineoEnum: string
{
    case A_POS = 'A+';
    case B_POS = 'B+';
    case AB_POS = 'AB+';
    case O_POS = 'O+';
    case A_NEG = 'A-';
    case B_NEG = 'B-';
    case AB_NEG = 'AB-';
    case O_NEG = 'O-';
}
