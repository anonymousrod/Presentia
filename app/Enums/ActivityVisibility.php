<?php

namespace App\Enums;

enum ActivityVisibility: string
{
    case ALL = 'ALL';
    case GROUP = 'GROUP';
    case ROLE = 'ROLE';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'Tout le monde',
            self::GROUP => 'Groupe spécifique',
            self::ROLE => 'Rôle spécifique',
        };
    }
}
