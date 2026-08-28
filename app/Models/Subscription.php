<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasHashid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory, HasHashid, Auditable;

    protected $fillable = [
        'church_id',
        'starts_at',
        'expires_at',
        'amount',
        'plan_name',
        'payment_method',
        'payment_reference',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'amount'     => 'integer',
    ];

    /**
     * Église bénéficiaire de l'abonnement.
     */
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    /**
     * Administrateur/SuperAdmin ayant enregistré l'abonnement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Montant formaté en FCFA.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Vérifie si l'abonnement est actif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }
}
