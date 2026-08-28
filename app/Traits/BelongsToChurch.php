<?php

namespace App\Traits;

use App\Models\Church;
use App\Models\Scopes\ChurchScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToChurch
{
    /**
     * Boot le trait pour injecter le scope et assigner automatiquement l'église courante.
     */
    public static function bootBelongsToChurch(): void
    {
        static::addGlobalScope(new ChurchScope());

        static::creating(function ($model) {
            if (empty($model->church_id)) {
                $effectiveChurchId = session('tenant_church_id') ?? (auth()->check() ? auth()->user()?->church_id : null);
                if ($effectiveChurchId) {
                    $model->church_id = $effectiveChurchId;
                }
            }
        });
    }

    /**
     * Relation vers l'église propriétaire de l'entité.
     */
    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
