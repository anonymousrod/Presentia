<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'identifiant' => ['required', 'string'],
            'password'    => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // 1. Protection contre le brute-force (Rate Limiting)
        $this->ensureIsNotRateLimited();

        // 2. Détection dynamique email vs téléphone
        $credentials = $this->getCredentials();

        $remember = $this->boolean('remember');

        // 3. Tentative de connexion
        if (!Auth::attempt($credentials, $remember)) {
            // Incrémenter le compteur de tentatives (blocage de 15 minutes = 900 secondes)
            RateLimiter::hit($this->throttleKey(), 900);

            // Déclencher l'événement Lockout si trop de tentatives
            if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
                event(new Lockout($this));
            }

            // Message d'erreur générique obligatoire (sécurité)
            throw ValidationException::withMessages([
                'identifiant' => __('auth.failed'),
            ]);
        }

        // 4. Réinitialiser le limiteur de débit en cas de succès
        RateLimiter::clear($this->throttleKey());

        // 5. Régénération obligatoire de la session (protection contre la fixation de session)
        $this->session()->regenerate();
    }

    /**
     * Détecte automatiquement si l'identifiant est un email ou un téléphone.
     */
    private function getCredentials(): array
    {
        $identifiant = $this->input('identifiant');
        $password = $this->input('password');

        if (filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
            return [
                'email'    => $identifiant,
                'password' => $password,
            ];
        }

        return [
            'phone'    => $identifiant,
            'password' => $password,
        ];
    }

    /**
     * Vérifie si l'utilisateur n'a pas dépassé le nombre maximal de tentatives.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifiant' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Clé de limitation de débit unique pour l'identifiant et l'adresse IP.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('identifiant')) . '|' . $this->ip());
    }
}
