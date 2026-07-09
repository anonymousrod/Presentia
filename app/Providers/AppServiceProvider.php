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
        } elseif (env('WHATSAPP_DRIVER') === 'ultramsg') {
            $this->app->bind(
                \App\Services\WhatsAppServiceInterface::class,
                \App\Services\UltraMsgWhatsAppService::class
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
        // Force HTTPS if APP_URL is set to https (like with Ngrok)
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Use Bootstrap 5 pagination styles globally
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Share app settings globally
        view()->composer('*', function ($view) {
            $settings = \App\Models\AppSetting::firstOrCreate(['id' => 1]);

            // Generate URLs for all image fields so views don't have to check storage vs assets
            $imageFields = [
                'favicon', 'logo_sm', 'logo_dark', 'logo_light',
                'pdf_logo_1', 'pdf_logo_2', 'default_avatar', 'default_cover',
                'sidebar_bg_1', 'sidebar_bg_2', 'sidebar_bg_3', 'sidebar_bg_4',
                'auth_bg'
            ];

            $defaults = [
                'favicon' => 'favicon.ico',
                'logo_sm' => 'logo-sm.png',
                'logo_dark' => 'logo-dark.png',
                'logo_light' => 'logo-light.png',
                'pdf_logo_1' => 'Icone J-EBER.png',
                'pdf_logo_2' => 'Icone J-EBER.png',
                'default_avatar' => 'users/avatar-1.jpg',
                'default_cover' => 'profile-bg.jpg',
                'sidebar_bg_1' => 'sidebar/img-1.jpg',
                'sidebar_bg_2' => 'sidebar/img-2.jpg',
                'sidebar_bg_3' => 'sidebar/img-3.jpg',
                'sidebar_bg_4' => 'sidebar/img-4.jpg',
                'auth_bg' => 'auth-one-bg.jpg',
            ];

            foreach ($imageFields as $field) {
                $urlField = $field . '_url';
                $value = $settings->$field;
                if ($value) {
                    if (str_starts_with($value, 'assets/') || $value === 'Icone J-EBER.png') {
                        $settings->$urlField = asset('assets/images/' . str_replace('assets/images/', '', $value));
                    } else {
                        $settings->$urlField = asset('storage/' . $value);
                    }
                } else {
                    // Fallback par défaut si la BDD est vide (ex: après migrate:fresh)
                    $settings->$urlField = asset('assets/images/' . $defaults[$field]);
                }
            }

            $view->with('appSettings', $settings);
        });

        // Bypass implicit pour l'administrateur
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrateur') ? true : null;
        });

        // Gate manage-users requise pour la protection des routes d'administration
        \Illuminate\Support\Facades\Gate::define('manage-users', function ($user) {
            return $user->hasRole('Administrateur')
                || $user->hasPermissionTo('member.view')
                || $user->hasPermissionTo('member.create')
                || $user->hasPermissionTo('member.edit');
        });

        // Gate access-activities requise pour la gestion des activités
        \Illuminate\Support\Facades\Gate::define('access-activities', function ($user) {
            return $user->hasRole('Administrateur')
                || $user->hasPermissionTo('activity.view')
                || $user->hasPermissionTo('activity.create')
                || $user->hasPermissionTo('activity.edit');
        });

        // Gate access-group-management requise pour la gestion des groupes (Administrateurs ou Chefs de groupe)
        \Illuminate\Support\Facades\Gate::define('access-group-management', function ($user) {
            return $user->hasRole('Administrateur')
                || $user->hasPermissionTo('group.view')
                || $user->hasPermissionTo('group.view_own');
        });

        // Enregistrement de l'Observer User
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Enregistrement de l'Observer Group
        \App\Models\Group::observe(\App\Observers\GroupObserver::class);

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
