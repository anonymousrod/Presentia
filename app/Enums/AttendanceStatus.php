<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'PRESENT';
    case LATE = 'LATE';
    case ABSENT = 'ABSENT';
    case EXCUSED = 'EXCUSED';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'Présent',
            self::LATE => 'En retard',
            self::ABSENT => 'Absent',
            self::EXCUSED => 'Excusé',
        };
    }
}
