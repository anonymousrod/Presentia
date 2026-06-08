<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendActivityReminder;
use App\Models\Registration;
use App\Enums\RegistrationStatus;

Schedule::command('activities:archive-old')->daily();
Schedule::command('app:expire-password-reset-requests')->daily();

Schedule::call(function () {
    // Récupérer les inscriptions actives (is_waitlisted = false, status != ABSENT_JUSTIFIED)
    // d'activités commençant dans précisément 24h, et dispatcher le job
    $targetTimeMin = now()->addHours(24)->startOfHour();
    $targetTimeMax = now()->addHours(24)->endOfHour();

    $registrations = Registration::where('is_waitlisted', false)
        ->where('status', '!=', RegistrationStatus::ABSENT_JUSTIFIED)
        ->whereHas('activity', function ($query) use ($targetTimeMin, $targetTimeMax) {
            $query->whereBetween('start_time', [$targetTimeMin, $targetTimeMax]);
        })->with(['user', 'activity'])->get();

    foreach ($registrations as $reg) {
        dispatch(new SendActivityReminder($reg->user, $reg->activity));
    }
})->dailyAt('09:00');
