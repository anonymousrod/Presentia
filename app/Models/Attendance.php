<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class Attendance extends Model
{
    use HasFactory;
    use Auditable;
    use BelongsToChurch;

    protected $fillable = [
        'church_id',
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

    /**
     * Personnalise le journal d'audit pour les présences (action scan_qr si scan_source === 'qr_code').
     */
    protected static function logAuditActivity(string $action, Model $model): void
    {
        if ($action === 'created' && $model->scan_source === 'qr_code') {
            $action = 'scan_qr';
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'created' || $action === 'scan_qr') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'updated') {
            $changes = $model->getChanges();
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $value) {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }

            if (empty($newValues)) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        \App\Services\AuditService::log($action, $model, $oldValues, $newValues);
    }
}
