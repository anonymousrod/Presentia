<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the Auditable trait for the model.
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            static::logAuditActivity('created', $model);
        });

        static::updated(function (Model $model) {
            if ($model->wasChanged()) {
                static::logAuditActivity('updated', $model);
            }
        });

        static::deleted(function (Model $model) {
            static::logAuditActivity('deleted', $model);
        });
    }

    /**
     * Log the audit trail activity.
     */
    protected static function logAuditActivity(string $action, Model $model): void
    {
        // Sensitive fields blacklist that must NEVER be recorded
        $blacklist = [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'two_factor_confirmed_at',
            'passkey',
            'secret',
            'token',
        ];

        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = collect($model->getAttributes())
                ->except($blacklist)
                ->map(function ($value) {
                    if ($value instanceof \DateTimeInterface) {
                        return $value->format('Y-m-d H:i:s');
                    }
                    if ($value instanceof \UnitEnum) {
                        return $value->value;
                    }
                    return $value;
                })
                ->toArray();
        } elseif ($action === 'updated') {
            $changes = $model->getChanges();
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $value) {
                if (in_array($key, $blacklist)) {
                    continue;
                }

                $original = $model->getOriginal($key);

                // Format values for clean serialization
                $oldValues[$key] = $original instanceof \DateTimeInterface ? $original->format('Y-m-d H:i:s') : $original;
                $newValues[$key] = $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
            }

            if (empty($newValues)) {
                return; // Nothing to log after blacklist filtering
            }
        } elseif ($action === 'deleted') {
            $oldValues = collect($model->getAttributes())
                ->except($blacklist)
                ->map(function ($value) {
                    if ($value instanceof \DateTimeInterface) {
                        return $value->format('Y-m-d H:i:s');
                    }
                    if ($value instanceof \UnitEnum) {
                        return $value->value;
                    }
                    return $value;
                })
                ->toArray();
        }

        AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => Request::ip() ?? '127.0.0.1',
            'user_agent'     => Request::userAgent() ?? 'Console',
        ]);
    }
}
