<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Si le statut de l'utilisateur est PENDING, et qu'il n'est pas déjà sur la route autorisée de déconnexion ou de changement de mot de passe
            if ($user->status === UserStatus::PENDING && !$request->is('password/change', 'logout')) {
                return redirect()->route('password.change.show')
                    ->with('warning', "Vous devez obligatoirement modifier votre mot de passe pour activer votre compte lors de votre première connexion.");
            }
        }

        return $next($request);
    }
}
