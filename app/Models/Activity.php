<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'visibility',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'responsible_id',
        'cancellation_reason',
        'qr_version',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'type'       => \App\Enums\ActivityType::class,
        'status'     => \App\Enums\ActivityStatus::class,
    ];

    /**
     * Le responsable de l'activité.
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Les inscriptions à cette activité.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Les présences enregistrées.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
