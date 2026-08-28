<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Champs sensibles à ne jamais logger.
     */
    protected static array $blacklist = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'passkey',
        'secret',
        'token',
    ];

    /**
     * Enregistre une action d'audit. Fail-safe : si une exception survient, on la logue mais l'action principale continue.
     */
    public static function log(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            $churchId = session('tenant_church_id') ?? ($model && isset($model->church_id) ? $model->church_id : Auth::user()?->church_id);

            AuditLog::create([
                'church_id'      => $churchId,
                'user_id'        => Auth::id(),
                'action'         => $action,
                'auditable_type' => $model ? get_class($model) : null,
                'auditable_id'   => $model ? $model->getKey() : null,
                'old_values'     => self::filterSensitiveData($oldValues),
                'new_values'     => self::filterSensitiveData($newValues),
                'ip_address'     => Request::ip() ?? '127.0.0.1',
                'user_agent'     => Request::userAgent() ?? 'Console',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du log d\'audit: ' . $e->getMessage());
        }
    }

    /**
     * Filtrer les données sensibles d'un tableau de valeurs.
     */
    protected static function filterSensitiveData(?array $data): ?array
    {
        if (is_null($data)) {
            return null;
        }

        $filtered = [];

        foreach ($data as $key => $value) {
            if (in_array($key, self::$blacklist)) {
                continue;
            }

            if ($value instanceof \DateTimeInterface) {
                $filtered[$key] = $value->format('Y-m-d H:i:s');
            } elseif ($value instanceof \UnitEnum) {
                $filtered[$key] = $value->value;
            } else {
                $filtered[$key] = $value;
            }
        }

        return empty($filtered) ? null : $filtered;
    }
}
