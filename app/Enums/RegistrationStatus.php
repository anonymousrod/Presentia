<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case PRESENT = 'PRESENT';
    case ABSENT_JUSTIFIED = 'ABSENT_JUSTIFIED';
    case UNCERTAIN = 'UNCERTAIN';
}
