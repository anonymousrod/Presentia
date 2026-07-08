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
        // Automatically assign default role to new users if they don't have any roles yet
        if ($user->roles()->count() === 0) {
            $defaultRole = \Spatie\Permission\Models\Role::where('code', 'default_user')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        }

        $plainPassword = $user->plain_password;

        if ($plainPassword) {
            if ($user->hasEmail()) {
                SendEmailCredentials::dispatch($user, $plainPassword);
            } 
            
            if ($user->hasPhone()) {
                SendWhatsAppCredentials::dispatch($user, $plainPassword);
            }
        }
    }
}
