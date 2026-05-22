<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Attendance extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'user_id',
        'activity_id',
        'status',
        'scan_source',
        'note',
        'scanned_at',
        'ip_address',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'status' => \App\Enums\AttendanceStatus::class,
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
