<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use App\Jobs\SendEmailCredentials;
use App\Jobs\SendWhatsAppCredentials;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $plainPassword = $user->plain_password;

        if ($plainPassword) {
            if ($user->hasEmail()) {
                SendEmailCredentials::dispatch($user, $plainPassword);
            } elseif ($user->hasPhone()) {
                SendWhatsAppCredentials::dispatch($user, $plainPassword);
            }
        }

        // 2. Création automatique du log d'audit
        AuditLog::create([
            'user_id'        => auth()->id(), // Admin connecté (ou null si CLI/Seeder)
            'action'         => 'created',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
            'old_values'     => null,
            'new_values'     => [
                'name'       => $user->name,
                'first_name' => $user->first_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'birth_date' => $user->birth_date?->toDateString(),
                'status'     => $user->status instanceof \UnitEnum ? $user->status->value : $user->status,
            ],
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // On pourrait auditer les modifications ici si nécessaire
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Audit de suppression
        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'deleted',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
            'old_values'     => [
                'name'       => $user->name,
                'first_name' => $user->first_name,
            ],
            'new_values'     => null,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}
