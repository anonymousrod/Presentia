<?php

namespace App\Console\Commands;

use App\Models\PasswordResetRequest;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpirePasswordResetRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-password-reset-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire les demandes de réinitialisation de mot de passe WhatsApp vieilles de plus de 24h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = PasswordResetRequest::where('status', 'PENDING')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'EXPIRED']);

        $this->info("{$count} demandes expirées ont été mises à jour.");
    }
}
