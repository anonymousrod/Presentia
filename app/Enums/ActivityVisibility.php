<?php

namespace App\Enums;

enum ActivityVisibility: string
{
    case ALL = 'ALL';
    case GROUP = 'GROUP';
    case ROLE = 'ROLE';
}
