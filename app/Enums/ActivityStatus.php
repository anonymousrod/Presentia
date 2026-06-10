<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case CANCELLED = 'CANCELLED';
    case ARCHIVED = 'ARCHIVED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::PUBLISHED => 'Publié',
            self::CANCELLED => 'Annulé',
            self::ARCHIVED => 'Archivé',
        };
    }
}
