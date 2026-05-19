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
