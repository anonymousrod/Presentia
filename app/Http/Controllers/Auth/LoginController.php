<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion de l'utilisateur.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect()->intended(config('fortify.home', '/dashboard'));
    }

    /**
     * Déconnecte l'utilisateur et invalide la session.
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
