<?php

namespace App\Enums;

enum ActivityType: string
{
    case CULTE = 'CULTE';
    case REUNION = 'REUNION';
    case FORMATION = 'FORMATION';
    case SORTIE = 'SORTIE';
    case AUTRE = 'AUTRE';

    public function label(): string
    {
        return match ($this) {
            self::CULTE => 'Culte',
            self::REUNION => 'Réunion',
            self::FORMATION => 'Formation',
            self::SORTIE => 'Sortie',
            self::AUTRE => 'Autre',
        };
    }
}
