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

    protected static function logAuditActivity(string $action, Model $model): void
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
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
