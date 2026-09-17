<?php

namespace App\Services;

use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class DeviceBadgeService
{
    public const COOKIE_NAME = 'presentia_device_badge';
    public const TOKEN_LIFETIME_DAYS = 365;

    /**
     * Enrôle l'appareil actuel comme badge de présence pour l'utilisateur.
     *
     * @param User $user
     * @param Request $request
     * @return array ['device' => TrustedDevice, 'cookie' => SymfonyCookie]
     */
    public function enrollDevice(User $user, Request $request): array
    {
        // 1. Génération d'un token cryptographiquement sécurisé de 64 caractères
        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);

        // 2. Détection des métadonnées de l'appareil
        $deviceName = $this->detectDeviceName($request);
        $userAgent = $request->userAgent() ?? 'Inconnu';
        $ip = $request->ip();

        // 3. Enregistrement en base de données
        $churchId = session('tenant_church_id') ?? $user->church_id;

        $device = TrustedDevice::create([
            'user_id'            => $user->id,
            'church_id'          => $churchId,
            'device_token_hash'  => $tokenHash,
            'device_name'        => $deviceName,
            'device_fingerprint' => $userAgent,
            'ip_address'         => $ip,
            'last_used_at'       => now(),
            'expires_at'         => now()->addDays(self::TOKEN_LIFETIME_DAYS),
            'is_active'          => true,
        ]);

        // 4. Création du cookie sécurisé longue durée (1 an)
        $cookie = cookie(
            self::COOKIE_NAME,
            $plainToken,
            60 * 24 * self::TOKEN_LIFETIME_DAYS, // Durée en minutes (1 an)
            '/',
            null,
            $request->isSecure(),
            true, // httpOnly
            false, // raw
            'Lax' // sameSite
        );

        return [
            'device' => $device,
            'cookie' => $cookie,
        ];
    }

    /**
     * Résout l'utilisateur associé au badge d'appareil présent dans la requête.
     *
     * @param Request $request
     * @return User|null
     */
    public function resolveUserFromCookie(Request $request): ?User
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (!$token || !is_string($token) || strlen($token) < 32) {
            return null;
        }

        $tokenHash = hash('sha256', $token);

        $device = TrustedDevice::where('device_token_hash', $tokenHash)
            ->active()
            ->unexpired()
            ->with('user')
            ->first();

        if (!$device || !$device->user) {
            return null;
        }

        // Mise à jour de la date de dernière utilisation et de l'IP
        $device->update([
            'last_used_at' => now(),
            'ip_address'   => $request->ip(),
        ]);

        return $device->user;
    }

    /**
     * Vérifie si le navigateur/appareil courant possède un cookie d'enrôlement valide pour cet utilisateur.
     *
     * @param User $user
     * @param Request $request
     * @return TrustedDevice|null
     */
    public function getCurrentEnrolledDevice(User $user, Request $request): ?TrustedDevice
    {
        $token = $request->cookie(self::COOKIE_NAME);
        if (!$token) {
            return null;
        }

        $tokenHash = hash('sha256', $token);

        return TrustedDevice::where('user_id', $user->id)
            ->where('device_token_hash', $tokenHash)
            ->active()
            ->unexpired()
            ->first();
    }

    /**
     * Révoque un appareil de confiance.
     */
    public function revokeDevice(User $user, int $deviceId): bool
    {
        $device = TrustedDevice::where('user_id', $user->id)->find($deviceId);
        if ($device) {
            $device->delete();
            return true;
        }
        return false;
    }

    /**
     * Détecte un nom d'appareil et de navigateur lisible et convivial.
     */
    public function detectDeviceName(Request $request): string
    {
        $ua = $request->userAgent() ?? '';

        $os = 'Appareil';
        if (preg_match('/iPhone/i', $ua)) {
            $os = 'iPhone';
        } elseif (preg_match('/iPad/i', $ua)) {
            $os = 'iPad';
        } elseif (preg_match('/Android/i', $ua)) {
            $os = 'Smartphone Android';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) {
            $os = 'Mac';
        } elseif (preg_match('/Windows/i', $ua)) {
            $os = 'PC Windows';
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux';
        }

        $browser = 'Navigateur';
        if (preg_match('/Chrome/i', $ua) && !preg_match('/Edg|OPR/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edg/i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/SamsungBrowser/i', $ua)) {
            $browser = 'Samsung Internet';
        }

        return "{$os} ({$browser})";
    }

    /**
     * Fournit des conseils spécifiques au navigateur actuel pour l'appareil photo.
     */
    public function getBrowserGuidance(Request $request): array
    {
        $ua = $request->userAgent() ?? '';
        $isIos = (bool) preg_match('/iPhone|iPad/i', $ua);
        $isAndroid = (bool) preg_match('/Android/i', $ua);
        $isSafari = (bool) (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua));
        $isChrome = (bool) (preg_match('/Chrome/i', $ua) && !preg_match('/Edg|OPR/i', $ua));

        $warning = null;
        if ($isIos && !$isSafari) {
            $warning = "Sur iPhone, l'appareil photo ouvre généralement Safari. Pour que le scan fonctionne sans connexion, nous vous conseillons d'ouvrir cette page et d'activer votre badge dans Safari.";
        } elseif ($isAndroid && !$isChrome) {
            $warning = "Sur Android, l'appareil photo ouvre généralement Google Chrome. Si votre appareil photo n'utilise pas ce navigateur, pensez à enrôler votre badge sur Chrome.";
        }

        return [
            'is_ios'      => $isIos,
            'is_android'  => $isAndroid,
            'is_safari'   => $isSafari,
            'is_chrome'   => $isChrome,
            'device_name' => $this->detectDeviceName($request),
            'warning'     => $warning,
        ];
    }
}
