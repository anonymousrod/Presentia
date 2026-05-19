<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
    }
}
