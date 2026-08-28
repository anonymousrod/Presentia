<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasHashid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Church extends Model
{
    use HasFactory, SoftDeletes, HasHashid, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'logo_path',
        'status',
        'subscription_starts_at',
        'subscription_expires_at',
        'subscription_amount',
        'subscription_plan',
        'max_users',
        'max_groups',
        'notes',
    ];

    protected $casts = [
        'subscription_starts_at'  => 'datetime',
        'subscription_expires_at' => 'datetime',
        'subscription_amount'     => 'integer',
        'max_users'               => 'integer',
        'max_groups'              => 'integer',
    ];

    /**
     * Utilisateurs appartenant à cette église.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Groupes / Ministères de cette église.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Activités organisées par cette église.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Historique des abonnements de cette église.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->orderBy('expires_at', 'desc');
    }

    /**
     * Dernier abonnement actif.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Paramètres spécifiques de l'église (Logo, liens, etc.).
     */
    public function appSetting(): HasOne
    {
        return $this->hasOne(AppSetting::class);
    }

    /**
     * Vérifie si l'église et son abonnement sont actifs.
     */
    public function isSubscriptionActive(): bool
    {
        if ($this->status === 'suspended') {
            return false;
        }

        if (!$this->subscription_expires_at) {
            return false;
        }

        return $this->subscription_expires_at->isFuture();
    }

    /**
     * Nombre de jours restants avant l'expiration de l'abonnement annuel.
     */
    public function daysLeftInSubscription(): ?int
    {
        if (!$this->subscription_expires_at) {
            return null;
        }

        if ($this->subscription_expires_at->isPast()) {
            return 0;
        }

        return (int) Carbon::now()->diffInDays($this->subscription_expires_at, false);
    }

    /**
     * Vérifie si l'abonnement expire dans moins de 30 jours (pour déclencher l'alerte).
     */
    public function expiresInLessThan30Days(): bool
    {
        $days = $this->daysLeftInSubscription();
        return $days !== null && $days > 0 && $days <= 30;
    }

    /**
     * URL du logo ou logo par défaut.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && file_exists(public_path('storage/' . $this->logo_path))) {
            return asset('storage/' . $this->logo_path);
        }

        return asset('assets/images/logo-sm.png');
    }
}
