<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Registration extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'user_id',
        'activity_id',
        'status',
        'justification',
        'registered_at',
        'is_waitlisted',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'is_waitlisted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
