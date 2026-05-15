<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'message_type',
        'status',
        'provider_response',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'created_at'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
