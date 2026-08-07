<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasHashid;

class Role extends SpatieRole
{
    use HasHashid;
}
