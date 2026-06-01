<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Events\GroupLeaderAssigned;
use App\Listeners\AssignGroupLeaderRole;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('testing') || env('WHATSAPP_DRIVER') === 'fake') {
            $this->app->bind(
                \App\Services\WhatsAppServiceInterface::class,
                \App\Services\FakeWhatsAppService::class
            );
        } else {
            $this->app->bind(
                \App\Services\WhatsAppServiceInterface::class,
                \App\Services\D7NetworksWhatsAppService::class
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bypass implicit pour l'administrateur
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrateur') ? true : null;
        });

        // Gate manage-users requise pour la protection des routes d'administration
        \Illuminate\Support\Facades\Gate::define('manage-users', function ($user) {
            return $user->hasRole('Administrateur') || $user->hasPermissionTo('member.create');
        });

        // Enregistrement de l'Observer User
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Limiteur de débit pour la connexion (5 tentatives max, blocage de 15 minutes)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 5)->by(
                Str::transliterate(Str::lower($request->input('identifiant')) . '|' . $request->ip())
            );
        });

        // Enregistrement de l'événement de désignation du chef de groupe
        Event::listen(
            GroupLeaderAssigned::class,
            AssignGroupLeaderRole::class
        );
    }
}
