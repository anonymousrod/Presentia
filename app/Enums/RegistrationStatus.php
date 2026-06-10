<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PRESENT = 'PRESENT';
    case ABSENT_JUSTIFIED = 'ABSENT_JUSTIFIED';
    case UNCERTAIN = 'UNCERTAIN';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'Inscrit',
            self::ABSENT_JUSTIFIED => 'Désinscrit',
            self::UNCERTAIN => 'Incertain',
        };
    }
}
