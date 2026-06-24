<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Activity extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'title',
        'description',
        'activity_type_id',
        'status',
        'visibility',
        'visibility_group_id',
        'visibility_role_id',
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
        // 'type' is now a relation
        'status'     => \App\Enums\ActivityStatus::class,
        'visibility' => \App\Enums\ActivityVisibility::class,
    ];

    /**
     * Le type de l'activité.
     */
    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * Le responsable de l'activité.
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Le groupe ciblé par la visibilité.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'visibility_group_id');
    }

    /**
     * Le rôle ciblé par la visibilité.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'visibility_role_id');
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
