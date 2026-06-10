<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Création des groupes et affectations...');

        // Récupérer les chefs
        $chef1 = User::where('email', 'chef1@eber.org')->first();
        $chef2 = User::where('email', 'chef2@eber.org')->first();
        $chef3 = User::where('email', 'chef3@eber.org')->first();

        // Récupérer les jeunes
        $jeunes = User::whereIn('email', [
            'jeune1@eber.org',
            'jeune2@eber.org',
            'jeune3@eber.org',
            'jeune4@eber.org',
            'jeune5@eber.org',
            'jeune6@eber.org',
            'jeune7@eber.org'
        ])->orderBy('id', 'asc')->get();

        if (!$chef1 || !$chef2 || !$chef3 || $jeunes->isEmpty()) {
            $this->command->error('❌ Échec : Les utilisateurs nécessaires sont introuvables.');
            return;
        }

        // 1. Création des groupes
        $groupA = Group::firstOrCreate(
            ['name' => 'Groupe Flambeaux'],
            [
                'description' => 'Groupe des garçons éclaireurs (Flambeaux)',
                'category' => 'Flambeaux',
                'leader_id' => $chef1->id,
            ]
        );

        $groupB = Group::firstOrCreate(
            ['name' => "Groupe Claires de l'Éternel"],
            [
                'description' => 'Groupe des filles éclaireuses',
                'category' => 'Claires',
                'leader_id' => $chef2->id,
            ]
        );

        $groupC = Group::firstOrCreate(
            ['name' => 'Groupe Aînés'],
            [
                'description' => 'Groupe des jeunes aînés et aînées de la paroisse',
                'category' => 'Aînés',
                'leader_id' => $chef3->id,
            ]
        );

        // 2. Assigner les membres aux groupes
        // Group A members : Paul (chef), Jean (0), Alice (1), Emma (4)
        $groupA->members()->syncWithoutDetaching([
            $chef1->id => ['joined_at' => now()],
            $jeunes[0]->id => ['joined_at' => now()],
            $jeunes[1]->id => ['joined_at' => now()],
            $jeunes[4]->id => ['joined_at' => now()],
        ]);

        // Group B members : Sarah (chef), Bob (2), Charlie (3), Emma (4)
        $groupB->members()->syncWithoutDetaching([
            $chef2->id => ['joined_at' => now()],
            $jeunes[2]->id => ['joined_at' => now()],
            $jeunes[3]->id => ['joined_at' => now()],
            $jeunes[4]->id => ['joined_at' => now()],
        ]);

        // Group C members : Marc (chef), Julie (5)
        $groupC->members()->syncWithoutDetaching([
            $chef3->id => ['joined_at' => now()],
            $jeunes[5]->id => ['joined_at' => now()],
        ]);

        $this->command->info('✅ Groupes créés et membres assignés avec succès !');
    }
}
