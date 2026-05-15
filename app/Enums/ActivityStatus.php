<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case CANCELLED = 'CANCELLED';
    case ARCHIVED = 'ARCHIVED';
}
