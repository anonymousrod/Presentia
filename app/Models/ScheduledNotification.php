<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class ScheduledNotification extends Model
{
    use Auditable;
    use BelongsToChurch;

    protected $fillable = [
        'church_id',
        'sender_id',
        'target_type',
        'target_id',
        'title',
        'content',
        'channel',
        'scheduled_at',
        'sent_at',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * La cible de la notification (Group, User, etc.)
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
