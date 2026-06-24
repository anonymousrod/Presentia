<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChartDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Début du seeding des données de test pour les graphiques...');

        $groupsData = [
            'Béthel' => [
                'ADJAZO Dorcas', 'HOULO Ruth-Esther', 'BLECO Pontien', 'GAGNON Bienvenu', 
                'AGOSSOU Pierrette', 'KOUDOUKOUI Belvida', 'HOUNKPEVI Fresnel', 
                'HOUNDEKPONDJI Odyas', 'AIDEHOU Steve'
            ],
            'Cana' => [
                'YABI Béatrice', 'HOULO Mahounan Isabelle', 'AGBO Josaphat', 
                'HOUNDEKPONDJI Melyas', 'AHONKLOO Thierry', 'ADJOVI Doris', 
                'SOGBOSSI Marcellin', 'ADJAZO Raïssa', 'AZOMAN Acquilas', 'AMOUSSOU Françoise'
            ],
            'Éden' => [
                'KODONON Kenneth', 'EZEBADA Charlotte', 'HINLIN Ghislain'
            ],
            'Galilée' => [
                'KOUMONDJI Guy Morel', 'ATCHOUKE Estelle', 'OKESESAN Victoria', 
                'HOUNKANRIN Salomon', 'AZOMAN Kyria', 'METO Ulrich', 'SENA Georgette', 
                'KOUDOUKOUI Joy\'s', 'DOUTETIEN Alexine', 'DAGBEDJI Romario', 'ADIGBE Mardochée'
            ],
            'Salem' => [
                'Membre Salem 1', 'Membre Salem 2', 'Membre Salem 3', 'Membre Salem 4',
                'Membre Salem 5', 'Membre Salem 6', 'Membre Salem 7'
            ],
            'Shalom' => [
                'TETEGAN Exaucé', 'KIKI Emmanuel', 'DJOMATIN Béni-Christ', 'METOEVI Justin', 
                'HOUNGNINOU Merveille Élodie', 'AKALO Paulin', 'GNIMAGNON Espoir', 'DAGBELOU Joann'
            ],
            'Siloé' => [
                'CHABI Ashley', 'AYITCHEDEHOU Ezéchiel', 'AHOKPE Noëlie', 'ADJANOHOUN Esther', 
                'GNONLONFOUN Chantal', 'BLEKO Carine', 'AGOSSOU Pierre', 'KAHO Débora', 
                'AYITCHEDEHOU Obed', 'ASSAN Nelly', 'AHOKOU Anne'
            ],
            'Sinaï' => [
                'HESSOU Jules', 'IFATOUMA Abdias', 'ADJANOHOUN Néhémie', 'AYITCHEDEHOU Ezéchias', 
                'SOSSA Naomi', 'SEWLAN Lazare', 'DAVI Christian', 'BELMBAYE Frédéric'
            ],
        ];

        $defaultPassword = Hash::make('Password@1234!');

        foreach ($groupsData as $groupName => $members) {
            // Création du Chef de groupe
            $chefName = 'Chef ' . $groupName;
            $chef = User::firstOrCreate(
                ['email' => 'chef_' . Str::slug($groupName) . '@eber.org'],
                [
                    'name' => explode(' ', $chefName)[1] ?? 'Chef',
                    'first_name' => explode(' ', $chefName)[0],
                    'phone' => '+229' . rand(90000000, 99999999),
                    'password' => $defaultPassword,
                    'status' => 'ACTIVE',
                ]
            );
            $chef->syncRoles(['Jeune', 'Chef de groupe']);

            // Création du Groupe
            $group = Group::firstOrCreate(
                ['name' => 'Groupe ' . $groupName],
                [
                    'description' => 'Groupe ' . $groupName . ' créé pour les graphiques',
                    'category' => $groupName,
                    'leader_id' => $chef->id,
                ]
            );

            // Assigner le chef au groupe
            $group->members()->syncWithoutDetaching([$chef->id => ['joined_at' => now()]]);

            // Création des membres
            foreach ($members as $memberName) {
                $parts = explode(' ', $memberName, 2);
                $lastName = $parts[0];
                $firstName = $parts[1] ?? 'Prénom';

                $user = User::firstOrCreate(
                    ['email' => Str::slug($memberName) . '@eber.org'],
                    [
                        'name' => $lastName,
                        'first_name' => $firstName,
                        'phone' => '+229' . rand(90000000, 99999999),
                        'password' => $defaultPassword,
                        'status' => 'ACTIVE',
                    ]
                );
                $user->syncRoles(['Jeune']);
                
                // Assigner au groupe
                $group->members()->syncWithoutDetaching([$user->id => ['joined_at' => now()]]);
            }
        }

        $this->command->info('✅ Membres et Groupes créés avec succès.');

        // ---------------------------------------------------------
        // 2. CRÉATION DES ACTIVITÉS
        // ---------------------------------------------------------
        $this->command->info('📅 Création des activités (École de dimanche & Activités locales)...');

        $ecoleDimancheType = \App\Models\ActivityType::where('name', 'École de dimanche')->first();
        $reunionType = \App\Models\ActivityType::where('name', 'Réunion')->first();

        $ecoleDimancheDates = ['2026-02-22', '2026-03-01', '2026-03-08', '2026-03-15', '2026-03-22', '2026-03-29', '2026-04-19', '2026-04-26', '2026-05-31'];
        $ecoleDimancheActivities = [];
        
        foreach ($ecoleDimancheDates as $date) {
            $ecoleDimancheActivities[] = \App\Models\Activity::create([
                'title' => 'École de dimanche',
                'activity_type_id' => $ecoleDimancheType->id,
                'status' => 'PUBLISHED',
                'start_time' => $date . ' 09:00:00',
                'end_time' => $date . ' 11:30:00',
                'capacity' => 100,
            ]);
        }

        $localesData = [
            ['Lancement', '2026-01-18'],
            ['Groupe de parole', '2026-02-05'],
            ['Séminaire', '2026-02-21'],
            ['Rencontre Anciens & Pasteurs', '2026-02-22'],
            ['Jeûne et prière', '2026-03-13'],
        ];
        $localesActivities = [];

        foreach ($localesData as $data) {
            $localesActivities[] = \App\Models\Activity::create([
                'title' => $data[0],
                'activity_type_id' => $reunionType->id,
                'status' => 'PUBLISHED',
                'start_time' => $data[1] . ' 18:00:00',
                'end_time' => $data[1] . ' 20:00:00',
                'capacity' => 100,
            ]);
        }

        // ---------------------------------------------------------
        // 3. CRÉATION DES PRÉSENCES (Aléatoires pour les tests)
        // ---------------------------------------------------------
        $this->command->info('✍️ Assignation des présences aléatoires...');
        
        $allUsers = User::whereHas('roles', function($q){ $q->where('name', 'Jeune'); })->get();
        
        // Présences pour l'École de dimanche
        foreach ($ecoleDimancheActivities as $activity) {
            $attendeesCount = rand(10, 27); // Selon le graphe 1 (entre 10 et 27)
            $attendees = $allUsers->random(min($attendeesCount, $allUsers->count()));
            
            foreach ($attendees as $attendee) {
                \App\Models\Attendance::create([
                    'user_id' => $attendee->id,
                    'activity_id' => $activity->id,
                    'status' => 'PRESENT',
                    'scan_source' => 'manual',
                ]);
            }
        }

        // Présences pour les Activités locales
        foreach ($localesActivities as $activity) {
            $attendeesCount = rand(20, 45); 
            $attendees = $allUsers->random(min($attendeesCount, $allUsers->count()));
            
            foreach ($attendees as $attendee) {
                \App\Models\Attendance::create([
                    'user_id' => $attendee->id,
                    'activity_id' => $activity->id,
                    'status' => 'PRESENT',
                    'scan_source' => 'manual',
                ]);
            }
        }

        $this->command->info('🎉 Seeding des graphiques terminé avec succès !');
    }
}
