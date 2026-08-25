<?php

namespace Database\Seeders;

use App\Models\Contribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Remittance;
use Illuminate\Support\Facades\Schema;

class ContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Nettoyage des anciennes contributions et versements...');
        Schema::disableForeignKeyConstraints();
        Contribution::truncate();
        Remittance::truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('🌱 Attribution des cotisations hebdomadaires et remplissage des contributions...');

        // 1. Définir une cotisation hebdomadaire pour tous les utilisateurs s'ils n'en ont pas
        $users = User::all();
        $possibleAmounts = [500, 1000, 1500, 2000];

        // Utilisateur collecteur par défaut
        $defaultCollector = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Trésorier', 'Chef de groupe']);
        })->first() ?? $users->first();

        foreach ($users as $user) {
            if (!$user->weekly_contribution || $user->weekly_contribution <= 0) {
                // Attribuer une cotisation hebdomadaire aléatoire (ex: 500, 1000, 1500, 2000 FCFA)
                $weeklyContribution = $possibleAmounts[array_rand($possibleAmounts)];
                $user->update([
                    'weekly_contribution' => $weeklyContribution,
                ]);
            }
        }

        // Recharger les utilisateurs avec leur cotisation mise à jour
        $users = User::all();

        // 2. Générer les contributions de Février (02) à Juillet (07) pour l'année en cours
        $year = Carbon::now()->format('Y');

        for ($month = 2; $month <= 7; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $startOfMonth = Carbon::parse("$year-$monthStr-01")->startOfMonth();
            $endOfMonth = Carbon::parse("$year-$monthStr-01")->endOfMonth();

            // Trouver tous les dimanches du mois
            $sundays = [];
            $date = $startOfMonth->copy()->next(Carbon::SUNDAY);
            if ($startOfMonth->isSunday()) {
                $date = $startOfMonth->copy();
            }
            while ($date->lte($endOfMonth)) {
                $sundays[] = $date->copy();
                $date->addWeek();
            }

            foreach ($users as $user) {
                $amount = $user->weekly_contribution;
                if ($amount <= 0) {
                    continue;
                }

                // Récupérer le groupe et le responsable/collecteur du groupe si disponible
                $userGroup = $user->groups()->first();
                $collectedBy = $userGroup ? ($userGroup->collector_id ?? $userGroup->leader_id ?? $defaultCollector->id) : $defaultCollector->id;

                foreach ($sundays as $sunday) {
                    Contribution::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $sunday->toDateString(),
                        ],
                        [
                            'collected_by' => $collectedBy,
                            'amount' => $amount,
                            'remittance_id' => null,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Contributions de Février à Juillet créées avec succès !');
    }
}
