<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\Church;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\Registration;
use App\Models\Remittance;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MinontinChurchSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⛪ Début du seeding pour : Église Baptiste de Minontin...');

        // 1. CRÉATION OU RÉCUPÉRATION DE L'ÉGLISE
        $church = Church::firstOrCreate(
            ['slug' => 'minontin'],
            [
                'name'                   => 'Église Baptiste de Minontin',
                'code'                   => 'MIN-001',
                'email'                  => 'contact@minontin.org',
                'phone'                  => '+229 97 45 88 12',
                'address'                => 'Quartier Minontin, Rue 142',
                'city'                   => 'Cotonou',
                'status'                 => 'active',
                'subscription_starts_at' => Carbon::now(),
                'subscription_expires_at'=> Carbon::now()->addYear(), // Abonnement de 1 an
                'subscription_amount'    => 150000,
                'subscription_plan'      => 'Annuel (1 an)',
                'notes'                  => 'Deuxième église cliente sur la plateforme Presentia.',
            ]
        );

        // 2. ENREGISTREMENT DE L'ABONNEMENT ANNUEL INITIAL (1 AN)
        Subscription::firstOrCreate(
            [
                'church_id' => $church->id,
                'plan_name' => 'Abonnement Annuel (1 an)',
            ],
            [
                'starts_at'         => Carbon::now(),
                'expires_at'        => Carbon::now()->addYear(),
                'amount'            => 150000,
                'payment_method'    => 'MTN Mobile Money',
                'payment_reference' => 'MIN-SUB-' . Carbon::now()->format('Ymd'),
                'status'            => 'active',
                'notes'             => 'Abonnement annuel initial d\'un an payé par MoMo.',
            ]
        );

        // 3. PARAMÈTRES PAR DÉFAUT DE L'ÉGLISE
        AppSetting::firstOrCreate(
            ['church_id' => $church->id],
            [
                'hero_title'      => 'Bienvenue à l\'Église Baptiste de Minontin',
                'hero_subtitle'   => 'Une jeunesse unie, vivante et engagée pour l\'Évangile.',
                'about_mission'   => 'Former une génération de disciples solides et engagés.',
                'contact_phone'   => '+229 97 45 88 12',
            ]
        );

        // 3.1 GÉNÉRATION DES RÔLES PROPRES À MINONTIN
        RolesAndPermissionsSeeder::seedRolesForChurch($church->id);
        setPermissionsTeamId($church->id);

        // 4. CRÉATION DU COMPTE ADMINISTRATEUR LOCAL
        $admin = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'admin@minontin.org'],
            [
                'church_id'  => $church->id,
                'first_name' => 'David',
                'name'       => 'HOUNSOU',
                'phone'      => '97458812',
                'password'   => Hash::make('Admin@1234!'),
                'status'     => 'ACTIVE',
            ]
        );
        if (!$admin->hasRole('Administrateur')) {
            $admin->assignRole('Administrateur');
        }

        // 5. CRÉATION DU TRÉSORIER GÉNÉRAL
        $treasurer = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'tresorier@minontin.org'],
            [
                'church_id'  => $church->id,
                'first_name' => 'Samuel',
                'name'       => 'DOSSOU',
                'phone'      => '96112233',
                'password'   => Hash::make('Tresor@1234!'),
                'status'     => 'ACTIVE',
                'weekly_contribution' => 1500,
            ]
        );
        if (!$treasurer->hasRole('Trésorier Général')) {
            $treasurer->assignRole('Trésorier Général');
        }

        // 6. CRÉATION DES GROUPES ET CHEFS / COLLECTEURS
        $groupData = [
            [
                'name'        => 'Groupe Emmanuel',
                'description' => 'Groupe des jeunes du secteur Nord Minontin',
                'color'       => '#4f46e5',
                'leader'      => ['first_name' => 'Daniel', 'name' => 'AGBO', 'email' => 'chef.emmanuel@minontin.org', 'phone' => '97010101'],
                'collector'   => ['first_name' => 'Grace', 'name' => 'ADANHO', 'email' => 'collecteur.emmanuel@minontin.org', 'phone' => '97010102'],
            ],
            [
                'name'        => 'Groupe Maranatha',
                'description' => 'Groupe des jeunes étudiants et lycéens',
                'color'       => '#059669',
                'leader'      => ['first_name' => 'Josué', 'name' => 'KOUDJO', 'email' => 'chef.maranatha@minontin.org', 'phone' => '97020201'],
                'collector'   => ['first_name' => 'Rachel', 'name' => 'TOHOUE', 'email' => 'collecteur.maranatha@minontin.org', 'phone' => '97020202'],
            ],
            [
                'name'        => 'Groupe Ebenezer',
                'description' => 'Groupe des jeunes travailleurs et professionnels',
                'color'       => '#d97706',
                'leader'      => ['first_name' => 'Caleb', 'name' => 'MENSAH', 'email' => 'chef.ebenezer@minontin.org', 'phone' => '97030301'],
                'collector'   => ['first_name' => 'Esther', 'name' => 'GBEDIGA', 'email' => 'collecteur.ebenezer@minontin.org', 'phone' => '97030302'],
            ],
        ];

        $createdGroups = [];

        foreach ($groupData as $gData) {
            // Création du Chef de Groupe
            $chef = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $gData['leader']['email']],
                [
                    'church_id'           => $church->id,
                    'first_name'          => $gData['leader']['first_name'],
                    'name'                => $gData['leader']['name'],
                    'phone'               => $gData['leader']['phone'],
                    'password'            => Hash::make('Pass@1234!'),
                    'status'              => 'ACTIVE',
                    'weekly_contribution' => 1000,
                ]
            );
            if (!$chef->hasRole('Chef de groupe')) {
                $chef->assignRole('Chef de groupe');
            }

            // Création du Chargé de Collecte
            $collector = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $gData['collector']['email']],
                [
                    'church_id'           => $church->id,
                    'first_name'          => $gData['collector']['first_name'],
                    'name'                => $gData['collector']['name'],
                    'phone'               => $gData['collector']['phone'],
                    'password'            => Hash::make('Pass@1234!'),
                    'status'              => 'ACTIVE',
                    'weekly_contribution' => 1000,
                ]
            );
            if (!$collector->hasRole('Chargé de collecte')) {
                $collector->assignRole('Chargé de collecte');
            }

            // Création du Groupe
            $group = Group::withoutGlobalScopes()->firstOrCreate(
                [
                    'church_id' => $church->id,
                    'name'      => $gData['name'],
                ],
                [
                    'description'  => $gData['description'],
                    'category'     => 'Jeunesse',
                    'color'        => $gData['color'],
                    'leader_id'    => $chef->id,
                    'collector_id' => $collector->id,
                ]
            );

            // Rattacher le chef et le collecteur au groupe
            $group->members()->syncWithoutDetaching([
                $chef->id      => ['joined_at' => now()->subMonths(6)],
                $collector->id => ['joined_at' => now()->subMonths(6)],
            ]);

            $createdGroups[] = [
                'group'     => $group,
                'collector' => $collector,
            ];
        }

        // 7. CRÉATION DES MEMBRES (JEUNES)
        $membersData = [
            ['first_name' => 'Marc', 'name' => 'AHOUANVO', 'email' => 'marc.ahouanvo@minontin.org', 'phone' => '97110001', 'amount' => 1000],
            ['first_name' => 'Jeanne', 'name' => 'TOSSOU', 'email' => 'jeanne.tossou@minontin.org', 'phone' => '97110002', 'amount' => 500],
            ['first_name' => 'Pierre', 'name' => 'GBAGUIDI', 'email' => 'pierre.gbaguidi@minontin.org', 'phone' => '97110003', 'amount' => 1500],
            ['first_name' => 'Rebecca', 'name' => 'KINNOU', 'email' => 'rebecca.kinnou@minontin.org', 'phone' => '97110004', 'amount' => 1000],
            ['first_name' => 'Paul', 'name' => 'HOUEDANOU', 'email' => 'paul.houedanou@minontin.org', 'phone' => '97110005', 'amount' => 2000],
            ['first_name' => 'Ruth', 'name' => 'SOGLO', 'email' => 'ruth.soglo@minontin.org', 'phone' => '97110006', 'amount' => 500],
            ['first_name' => 'Simon', 'name' => 'ZANNOU', 'email' => 'simon.zannou@minontin.org', 'phone' => '97110007', 'amount' => 1000],
            ['first_name' => 'Lydie', 'name' => 'DEGUENON', 'email' => 'lydie.deguenon@minontin.org', 'phone' => '97110008', 'amount' => 1500],
            ['first_name' => 'Moïse', 'name' => 'BIO', 'email' => 'moise.bio@minontin.org', 'phone' => '97110009', 'amount' => 1000],
            ['first_name' => 'Priscille', 'name' => 'LOKO', 'email' => 'priscille.loko@minontin.org', 'phone' => '97110010', 'amount' => 2000],
            ['first_name' => 'Elie', 'name' => 'AGOSSOU', 'email' => 'elie.agossou@minontin.org', 'phone' => '97110011', 'amount' => 500],
            ['first_name' => 'Deborah', 'name' => 'SAVI', 'email' => 'deborah.savi@minontin.org', 'phone' => '97110012', 'amount' => 1000],
            ['first_name' => 'Barnabé', 'name' => 'KPANOU', 'email' => 'barnabe.kpanou@minontin.org', 'phone' => '97110013', 'amount' => 1500],
            ['first_name' => 'Tabitha', 'name' => 'VODOUHE', 'email' => 'tabitha.vodouhe@minontin.org', 'phone' => '97110014', 'amount' => 1000],
            ['first_name' => 'Timothée', 'name' => 'ASSOGBA', 'email' => 'timothee.assogba@minontin.org', 'phone' => '97110015', 'amount' => 2000],
        ];

        $allMembers = [];

        foreach ($membersData as $index => $mData) {
            $user = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $mData['email']],
                [
                    'church_id'           => $church->id,
                    'first_name'          => $mData['first_name'],
                    'name'                => $mData['name'],
                    'phone'               => $mData['phone'],
                    'password'            => Hash::make('Pass@1234!'),
                    'status'              => 'ACTIVE',
                    'weekly_contribution' => $mData['amount'],
                ]
            );

            if (!$user->hasRole('Jeune')) {
                $user->assignRole('Jeune');
            }

            // Répartir équitablement dans les 3 groupes
            $targetGroup = $createdGroups[$index % count($createdGroups)]['group'];
            $targetGroup->members()->syncWithoutDetaching([
                $user->id => ['joined_at' => now()->subMonths(5)],
            ]);

            $allMembers[] = [
                'user'  => $user,
                'group' => $targetGroup,
            ];
        }

        // 8. CRÉATION DES TYPES D'ACTIVITÉS ET ACTIVITÉS
        $typeCulte = ActivityType::withoutGlobalScopes()->firstOrCreate(
            ['church_id' => $church->id, 'name' => 'Culte de Jeunesse'],
            ['color' => '#4f46e5']
        );
        $typeEtude = ActivityType::withoutGlobalScopes()->firstOrCreate(
            ['church_id' => $church->id, 'name' => 'Étude Biblique & Prière'],
            ['color' => '#059669']
        );
        $typeSortie = ActivityType::withoutGlobalScopes()->firstOrCreate(
            ['church_id' => $church->id, 'name' => 'Agapé & Sortie Détente'],
            ['color' => '#d97706']
        );

        $activity1 = Activity::withoutGlobalScopes()->firstOrCreate(
            ['church_id' => $church->id, 'title' => 'Culte Spécial de Louange & Témoignages'],
            [
                'description'             => 'Grand moment d\'adoration et de célébration avec toute la jeunesse de Minontin.',
                'activity_type_id'        => $typeCulte->id,
                'status'                  => 'PUBLISHED',
                'visibility'              => 'ALL',
                'start_time'              => Carbon::now()->subDays(10)->setTime(16, 0),
                'end_time'                => Carbon::now()->subDays(10)->setTime(18, 30),
                'location'                => 'Temple Principal de Minontin',
                'capacity'                => 100,
                'responsible_id'          => $admin->id,
                'is_registration_required'=> false,
            ]
        );

        $activity2 = Activity::withoutGlobalScopes()->firstOrCreate(
            ['church_id' => $church->id, 'title' => 'Retraite Spirituelle de Jeunesse 2026'],
            [
                'description'             => 'Thème : Enracinés et bâtis en Christ (Colossiens 2:7).',
                'activity_type_id'        => $typeEtude->id,
                'status'                  => 'PUBLISHED',
                'visibility'              => 'ALL',
                'start_time'              => Carbon::now()->addDays(15)->setTime(8, 30),
                'end_time'                => Carbon::now()->addDays(17)->setTime(17, 0),
                'location'                => 'Centre d\'accueil de Ouidah',
                'capacity'                => 60,
                'responsible_id'          => $admin->id,
                'is_registration_required'=> true,
            ]
        );

        // Inscriptions et présences pour l'activité passée
        foreach ($allMembers as $mObj) {
            $u = $mObj['user'];

            // Présence pour l'activité passée
            Attendance::withoutGlobalScopes()->firstOrCreate(
                ['church_id' => $church->id, 'activity_id' => $activity1->id, 'user_id' => $u->id],
                [
                    'status'      => 'PRESENT',
                    'scan_source' => 'qr_code',
                    'scanned_at'  => Carbon::now()->subDays(10)->setTime(15, 50),
                    'ip_address'  => '127.0.0.1',
                ]
            );

            // Inscription pour la retraite à venir
            Registration::withoutGlobalScopes()->firstOrCreate(
                ['church_id' => $church->id, 'activity_id' => $activity2->id, 'user_id' => $u->id],
                [
                    'status'        => 'PRESENT',
                    'registered_at' => Carbon::now()->subDays(3),
                ]
            );
        }

        // 9. GÉNÉRATION DES COTISATIONS HEBDOMADAIRES ET DES VERSEMENTS
        $sundays = [];
        $currentYear = Carbon::now()->year;
        $date = Carbon::create($currentYear, 2, 1);
        $endDate = Carbon::create($currentYear, 7, 31);

        while ($date <= $endDate) {
            if ($date->isSunday()) {
                $sundays[] = $date->copy();
            }
            $date->addDay();
        }

        // Pour chaque groupe, créer les cotisations et versements
        foreach ($createdGroups as $gItem) {
            $grp = $gItem['group'];
            $col = $gItem['collector'];
            $grpMembers = $grp->members()->get();

            // Par mois (Février à Juillet)
            for ($month = 2; $month <= 7; $month++) {
                $monthSundays = array_filter($sundays, fn($d) => $d->month === $month);
                if (empty($monthSundays)) continue;

                $monthContributions = [];

                foreach ($monthSundays as $sunDate) {
                    foreach ($grpMembers as $mb) {
                        $amount = $mb->weekly_contribution ?? 1000;
                        if ($amount <= 0) continue;

                        $contrib = Contribution::withoutGlobalScopes()->firstOrCreate(
                            [
                                'church_id' => $church->id,
                                'user_id'   => $mb->id,
                                'date'      => $sunDate->toDateString(),
                            ],
                            [
                                'amount'       => $amount,
                                'collected_by' => $col->id,
                            ]
                        );
                        $monthContributions[] = $contrib;
                    }
                }

                // Créer un versement validé pour les mois passés (Février à Juin) et en attente pour Juillet
                $totalAmount = array_sum(array_map(fn($c) => $c->amount, $monthContributions));

                if ($totalAmount > 0) {
                    $isPastMonth = ($month < 7);
                    $remittance = Remittance::withoutGlobalScopes()->create([
                        'church_id'    => $church->id,
                        'group_id'     => $grp->id,
                        'collector_id' => $col->id,
                        'treasurer_id' => $isPastMonth ? $treasurer->id : null,
                        'amount'       => $totalAmount,
                        'status'       => $isPastMonth ? 'validated' : 'pending',
                        'validated_at' => $isPastMonth ? Carbon::create($currentYear, $month, 28)->setTime(18, 0) : null,
                        'created_at'   => Carbon::create($currentYear, $month, 26)->setTime(17, 30),
                        'updated_at'   => Carbon::create($currentYear, $month, 28)->setTime(18, 0),
                    ]);

                    // Associer le versement aux contributions
                    foreach ($monthContributions as $c) {
                        $c->remittance_id = $remittance->id;
                        $c->save();
                    }
                }
            }
        }

        $this->command->info('✅ Église Baptiste de Minontin générée avec succès !');
        $this->command->info('   - Admin : admin@minontin.org / Admin@1234!');
        $this->command->info('   - Trésorier : tresorier@minontin.org / Tresor@1234!');
        $this->command->info('   - Groupes : Emmanuel, Maranatha, Ebenezer');
        $this->command->info('   - Membres : ' . (count($membersData) + 6) . ' utilisateurs');
        $this->command->info('   - Cotisations et versements de Février à Juillet créés');
    }
}
