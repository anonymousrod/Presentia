<?php

namespace App\Enums;

enum ActivityType: string
{
    case CULTE = 'CULTE';
    case REUNION = 'REUNION';
    case FORMATION = 'FORMATION';
    case SORTIE = 'SORTIE';
    case AUTRE = 'AUTRE';
}
