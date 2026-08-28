<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasHashid;

class Role extends SpatieRole
{
    use HasHashid;

    protected $fillable = [
        'church_id',
        'name',
        'guard_name',
        'code',
        'description',
        'is_system',
    ];

    public function church(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
