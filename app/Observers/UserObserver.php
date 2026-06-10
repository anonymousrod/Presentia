<?php

namespace App\Observers;

use App\Models\User;
use App\Jobs\SendEmailCredentials;
use App\Jobs\SendWhatsAppCredentials;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Automatically assign 'Jeune' role to new users if they don't have any roles yet
        if ($user->roles()->count() === 0 && \Spatie\Permission\Models\Role::where('name', 'Jeune')->exists()) {
            $user->assignRole('Jeune');
        }

        $plainPassword = $user->plain_password;

        if ($plainPassword) {
            if ($user->hasEmail()) {
                SendEmailCredentials::dispatch($user, $plainPassword);
            } elseif ($user->hasPhone()) {
                SendWhatsAppCredentials::dispatch($user, $plainPassword);
            }
        }
    }
}
