<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ChurchScope implements Scope
{
    protected static bool $isApplying = false;

    /**
     * Applique le scope global d'isolation par église.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Protection absolue contre toute boucle de récursion infinie
        if (self::$isApplying) {
            return;
        }

        if (!auth()->check()) {
            return;
        }

        self::$isApplying = true;

        try {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            // Si c'est un Super Admin, isoler sur l'église active (mode support ou son église locale) hors des routes super-admin
            if ($user->isSuperAdmin()) {
                $activeChurchId = session('tenant_church_id') ?? $user->church_id;
                if ($activeChurchId && !request()->is('super-admin*')) {
                    $builder->where($model->getTable() . '.church_id', $activeChurchId);
                }
                return;
            }

            // Si l'utilisateur appartient à une église, restreindre automatiquement les données
            if ($user->church_id) {
                $builder->where($model->getTable() . '.church_id', $user->church_id);
            }
        } catch (\Throwable $e) {
            // Fail-safe silencieux pour ne jamais bloquer le chargement
        } finally {
            self::$isApplying = false;
        }
    }
}
