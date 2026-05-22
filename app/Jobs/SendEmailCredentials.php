<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailCredentials implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * Nombre maximum de tentatives.
     */
    public int $tries = 3;

    /**
     * Délais de backoff exponentiel en secondes : 30s, 60s, 120s.
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public User $user;
    public string $plainPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->user->email) {
            Mail::to($this->user->email)->send(
                new UserCredentialsMail($this->user, $this->plainPassword)
            );
        }
    }
}
