<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration de remédiation sécurité — Backfill church_id = 1 sur toutes les tables tenant.
 *
 * Audit finding : La migration 2026_09_01_000003_add_church_id_to_tenant_tables a ajouté
 * la colonne church_id sans rétro-remplir les lignes existantes. Cela causait :
 *  - Une fuite cross-tenant : les 80 users avec church_id = NULL échappaient au ChurchScope
 *    et pouvaient voir les données de toutes les églises.
 *  - Une invisibilité de toutes les données métier (groupes, activités, cotisations…)
 *    pour un admin scopé église 1.
 *
 * Ce backfill fixe church_id = 1 (Eglise Eber) sur toutes les lignes NULL.
 * À ne jamais relancer si l'environnement est multi-tenant avec plusieurs églises réelles.
 */
return new class () extends Migration {
    /** ID de l'église par défaut (Eber — seule église en production) */
    private const CHURCH_ID = 1;

    /**
     * Tables à rétro-remplir avec leur condition WHERE.
     * Format : ['table' => whereClause]
     */
    private function tables(): array
    {
        return [
            'users'               => ['church_id' => null],
            'groups'              => ['church_id' => null],
            'activities'          => ['church_id' => null],
            'activity_types'      => ['church_id' => null],
            'contributions'       => ['church_id' => null],
            'attendances'         => ['church_id' => null],
            'app_settings'        => ['church_id' => null],
            'audit_logs'          => ['church_id' => null],
            'whatsapp_logs'       => ['church_id' => null],
        ];
    }

    public function up(): void
    {
        $churchId = self::CHURCH_ID;

        // Vérifier que l'église cible existe avant de procéder
        $churchExists = DB::table('churches')->where('id', $churchId)->exists();
        if (!$churchExists) {
            // Dans un environnement de test ou migrate:fresh où churches n'est pas encore seedé,
            // on passe simplement le backfill.
            return;
        }

        $totalUpdated = 0;
        foreach ($this->tables() as $table => $where) {
            // Vérifier que la table et la colonne existent
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }
            if (!DB::getSchemaBuilder()->hasColumn($table, 'church_id')) {
                continue;
            }

            $count = DB::table($table)->whereNull('church_id')->update(['church_id' => $churchId]);
            $totalUpdated += $count;

            if ($count > 0) {
                echo "  ✓ {$table} : {$count} ligne(s) mise(s) à jour\n";
            }
        }

        echo "  Total : {$totalUpdated} ligne(s) corrigée(s) dans " . count($this->tables()) . " tables.\n";
    }

    public function down(): void
    {
        // Le rollback remet church_id à NULL sur toutes les lignes qui valaient 1.
        // À utiliser avec précaution si de nouvelles données ont été créées depuis.
        foreach ($this->tables() as $table => $where) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }
            if (!DB::getSchemaBuilder()->hasColumn($table, 'church_id')) {
                continue;
            }
            DB::table($table)->where('church_id', self::CHURCH_ID)->update(['church_id' => null]);
        }
    }
};
