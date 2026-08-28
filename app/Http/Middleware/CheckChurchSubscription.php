<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChurchSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Configurer le contexte d'église pour Spatie Permission (isolation par église)
            $currentChurchId = session('tenant_church_id') ?? $user->church_id ?? null;
            if ($currentChurchId) {
                setPermissionsTeamId($currentChurchId);
            }

            // Le Super Admin a toujours accès global
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Vérification de l'église de l'utilisateur
            $church = $user->church;

            if ($church) {
                // Église suspendue manuellement
                if ($church->status === 'suspended') {
                    if ($request->routeIs('subscription.expired') || $request->routeIs('logout')) {
                        return $next($request);
                    }
                    return redirect()->route('subscription.expired')->with('error', 'L\'accès de votre église a été suspendu par l\'administration de la plateforme.');
                }

                // Abonnement annuel expiré
                if (!$church->isSubscriptionActive()) {
                    if ($request->routeIs('subscription.expired') || $request->routeIs('logout')) {
                        return $next($request);
                    }
                    return redirect()->route('subscription.expired');
                }
            }
        }

        return $next($request);
    }
}
