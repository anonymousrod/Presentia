<?php

namespace App\Enums;

enum UserStatus: string
{
    case PENDING = 'PENDING';
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case SUSPENDED = 'SUSPENDED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::SUSPENDED => 'Suspendu',
        };
    }
}
