<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class Registration extends Model
{
    use HasFactory;
    use Auditable;
    use BelongsToChurch;

    protected $fillable = [
        'church_id',
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
        'status' => \App\Enums\RegistrationStatus::class,
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
