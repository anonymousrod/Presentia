<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Activity;
use App\Models\Registration;
use App\Models\Attendance;
use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Enums\ActivityVisibility;
use App\Enums\RegistrationStatus;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Création des activités, inscriptions et pointages...');

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

        // Récupérer les groupes
        $groupA = Group::where('name', 'Groupe Flambeaux')->first();
        $groupB = Group::where('name', "Groupe Claires de l'Éternel")->first();
        $groupC = Group::where('name', 'Groupe Aînés')->first();

        if (!$chef1 || !$chef3 || !$groupA || !$groupC) {
            $this->command->error('❌ Échec : Les groupes ou chefs nécessaires sont introuvables.');
            return;
        }

        // 1. Création des activités
        // Activité 1 : Global (ALL), Publiée, aujourd'hui
        $actGlobal = Activity::firstOrCreate(
            ['title' => "Grand Rassemblement d'Ouverture"],
            [
                'description' => "Rassemblement de rentrée pour tous les groupes. Présence obligatoire.",
                'type' => ActivityType::CULTE,
                'status' => ActivityStatus::PUBLISHED,
                'visibility' => ActivityVisibility::ALL,
                'start_time' => now()->startOfDay()->addHours(14),
                'end_time' => now()->startOfDay()->addHours(16)->addMinutes(30),
                'location' => 'Grand Temple de Cotonou',
                'capacity' => 50,
                'responsible_id' => $chef1->id,
            ]
        );

        // Activité 2 : Groupe Flambeaux, Publiée, aujourd'hui
        $actGroupA = Activity::firstOrCreate(
            ['title' => 'Sortie Technique Flambeaux'],
            [
                'description' => 'Apprentissage du froissartage et construction en plein air.',
                'type' => ActivityType::SORTIE,
                'status' => ActivityStatus::PUBLISHED,
                'visibility' => ActivityVisibility::GROUP,
                'visibility_group_id' => $groupA->id,
                'start_time' => now()->startOfDay()->addHours(17),
                'end_time' => now()->startOfDay()->addHours(19)->addMinutes(30),
                'location' => 'Parc de la Forêt',
                'capacity' => 20,
                'responsible_id' => $chef1->id,
            ]
        );

        // Activité 3 : Groupe Aînés, Publiée, demain
        $actGroupC = Activity::firstOrCreate(
            ['title' => 'Cercle de Discussion Aînés'],
            [
                'description' => 'Partage et débat thématique autour des enjeux contemporains.',
                'type' => ActivityType::REUNION,
                'status' => ActivityStatus::PUBLISHED,
                'visibility' => ActivityVisibility::GROUP,
                'visibility_group_id' => $groupC->id,
                'start_time' => now()->addDay()->startOfDay()->addHours(10),
                'end_time' => now()->addDay()->startOfDay()->addHours(12),
                'location' => 'Salle Annexe 2',
                'capacity' => 15,
                'responsible_id' => $chef3->id,
            ]
        );

        // Activité 4 : Rôle-specific (Chef de groupe), Publiée, samedi prochain
        $chefRole = Role::where('name', 'Chef de groupe')->first();
        $actRole = Activity::firstOrCreate(
            ['title' => 'Conseil des Chefs de Groupe'],
            [
                'description' => 'Planification stratégique des activités trimestrielles.',
                'type' => ActivityType::FORMATION,
                'status' => ActivityStatus::PUBLISHED,
                'visibility' => ActivityVisibility::ROLE,
                'visibility_role_id' => $chefRole?->id,
                'start_time' => now()->next(\Carbon\Carbon::SATURDAY)->startOfDay()->addHours(14),
                'end_time' => now()->next(\Carbon\Carbon::SATURDAY)->startOfDay()->addHours(16),
                'location' => 'Bureau du Comité',
                'capacity' => 10,
                'responsible_id' => $chef3->id,
            ]
        );

        // Activité 5 : Global (ALL), Brouillon
        $actDraft = Activity::firstOrCreate(
            ['title' => 'Camping de Printemps'],
            [
                'description' => 'Grand camp annuel de Pâques. Plus d\'informations à venir.',
                'type' => ActivityType::SORTIE,
                'status' => ActivityStatus::DRAFT,
                'visibility' => ActivityVisibility::ALL,
                'start_time' => now()->addMonth()->startOfDay()->addHours(8),
                'end_time' => now()->addMonth()->addDays(5)->startOfDay()->addHours(17),
                'location' => 'Base de Plein Air Nature',
                'capacity' => 100,
                'responsible_id' => $chef1->id,
            ]
        );

        // 2. Inscriptions (Registrations)
        // Inscrire tous les jeunes et chefs à l'activité globale
        $allUsers = array_merge([$chef1, $chef2, $chef3], $jeunes->all());
        foreach ($allUsers as $user) {
            Registration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $actGlobal->id,
                ],
                [
                    'status' => RegistrationStatus::PRESENT,
                    'registered_at' => now()->subDays(2),
                    'is_waitlisted' => false,
                ]
            );
        }

        // Inscrire les membres de groupe A à l'activité de groupe A
        $groupAMembers = [$chef1, $jeunes[0], $jeunes[1], $jeunes[4]];
        foreach ($groupAMembers as $user) {
            Registration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $actGroupA->id,
                ],
                [
                    'status' => RegistrationStatus::PRESENT,
                    'registered_at' => now()->subDays(1),
                    'is_waitlisted' => false,
                ]
            );
        }

        // Inscrire les membres de groupe C à l'activité de groupe C
        $groupCMembers = [$chef3, $jeunes[5]];
        foreach ($groupCMembers as $user) {
            Registration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_id' => $actGroupC->id,
                ],
                [
                    'status' => RegistrationStatus::PRESENT,
                    'registered_at' => now()->subDays(1),
                    'is_waitlisted' => false,
                ]
            );
        }

        // 3. Présences (Attendances) sur l'activité globale pour simuler
        // chef1 : PRÉSENT (manuel)
        Attendance::firstOrCreate(
            ['user_id' => $chef1->id, 'activity_id' => $actGlobal->id],
            ['status' => AttendanceStatus::PRESENT, 'scan_source' => 'manual', 'note' => 'Arrivé en avance', 'scanned_at' => now(), 'ip_address' => '127.0.0.1']
        );
        // chef2 : LATE (qr_code)
        Attendance::firstOrCreate(
            ['user_id' => $chef2->id, 'activity_id' => $actGlobal->id],
            ['status' => AttendanceStatus::LATE, 'scan_source' => 'qr_code', 'scanned_at' => now()->subMinutes(15), 'ip_address' => '192.168.1.5']
        );
        // jeune1 (Jean) : PRESENT (qr_code)
        Attendance::firstOrCreate(
            ['user_id' => $jeunes[0]->id, 'activity_id' => $actGlobal->id],
            ['status' => AttendanceStatus::PRESENT, 'scan_source' => 'qr_code', 'scanned_at' => now()->subMinutes(30), 'ip_address' => '192.168.1.10']
        );
        // jeune2 (Alice) : ABSENT (non pointé par QR code, puis marqué absent)
        Attendance::firstOrCreate(
            ['user_id' => $jeunes[1]->id, 'activity_id' => $actGlobal->id],
            ['status' => AttendanceStatus::ABSENT, 'scan_source' => 'manual', 'scanned_at' => now(), 'ip_address' => '127.0.0.1']
        );
        // jeune3 (Bob) : EXCUSED (justification de son absence)
        Attendance::firstOrCreate(
            ['user_id' => $jeunes[2]->id, 'activity_id' => $actGlobal->id],
            ['status' => AttendanceStatus::EXCUSED, 'scan_source' => 'manual', 'note' => 'Malade (certificat médical)', 'scanned_at' => now(), 'ip_address' => '127.0.0.1']
        );

        // 4. Génération de 35 activités supplémentaires avec Faker
        $faker = \Faker\Factory::create('fr_FR');
        $allGroups = Group::all();
        $allChefs = User::role('Chef de groupe')->get();
        $activityTypes = ActivityType::cases();
        $activityStatuses = [ActivityStatus::PUBLISHED, ActivityStatus::DRAFT, ActivityStatus::CANCELLED];

        for ($i = 1; $i <= 35; $i++) {
            $responsible = $allChefs->isNotEmpty() ? $allChefs->random() : $chef1;
            $type = $activityTypes[array_rand($activityTypes)];
            $status = $activityStatuses[array_rand($activityStatuses)];

            // 70% chance of being PUBLISHED
            if (rand(1, 10) <= 7) {
                $status = ActivityStatus::PUBLISHED;
            }

            // Determine visibility
            $visibilityVal = rand(1, 3);
            $visibility = ActivityVisibility::ALL;
            $visGroupId = null;
            $visRoleId = null;

            if ($visibilityVal === 2 && $allGroups->isNotEmpty()) {
                $visibility = ActivityVisibility::GROUP;
                $visGroupId = $allGroups->random()->id;
            } elseif ($visibilityVal === 3) {
                $visibility = ActivityVisibility::ROLE;
                $visRoleId = Role::inRandomOrder()->first()?->id;
            }

            // Time: random within past 15 days to future 30 days
            $daysOffset = rand(-15, 30);
            $startTime = now()->addDays($daysOffset)->startOfDay()->addHours(rand(8, 18));
            $endTime = $startTime->copy()->addHours(rand(1, 4));

            $activity = Activity::create([
                'title' => "Activité : " . $faker->sentence(rand(3, 5)),
                'description' => $faker->paragraph(rand(2, 4)),
                'type' => $type,
                'status' => $status,
                'visibility' => $visibility,
                'visibility_group_id' => $visGroupId,
                'visibility_role_id' => $visRoleId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $faker->city() . ", " . $faker->streetAddress(),
                'capacity' => rand(15, 80),
                'responsible_id' => $responsible->id,
            ]);

            // Add registrations and attendances for this activity
            // If published or cancelled, add registrations
            if ($status === ActivityStatus::PUBLISHED || $status === ActivityStatus::CANCELLED) {
                // Retrieve eligible user IDs based on visibility
                $eligibleUserIds = [];

                if ($visibility === ActivityVisibility::GROUP && $visGroupId) {
                    $group = Group::find($visGroupId);
                    if ($group) {
                        $eligibleUserIds = $group->members()->pluck('users.id')->toArray();
                    }
                } elseif ($visibility === ActivityVisibility::ROLE && $visRoleId) {
                    $roleName = Role::find($visRoleId)?->name;
                    if ($roleName) {
                        $eligibleUserIds = User::role($roleName)->pluck('id')->toArray();
                    }
                } else {
                    $eligibleUserIds = User::role('Jeune')->pluck('id')->toArray();
                }

                if (!empty($eligibleUserIds)) {
                    // Register a random subset of eligible users
                    $registerCount = min(count($eligibleUserIds), rand(15, 45));
                    $shuffledUserIds = $eligibleUserIds;
                    shuffle($shuffledUserIds);
                    $registeredUserIds = array_slice($shuffledUserIds, 0, $registerCount);

                    foreach ($registeredUserIds as $index => $userId) {
                        // Registration status: 85% PRESENT, 10% ABSENT_JUSTIFIED, 5% UNCERTAIN
                        $randVal = rand(1, 100);
                        if ($randVal <= 85) {
                            $regStatus = RegistrationStatus::PRESENT;
                        } elseif ($randVal <= 95) {
                            $regStatus = RegistrationStatus::ABSENT_JUSTIFIED;
                        } else {
                            $regStatus = RegistrationStatus::UNCERTAIN;
                        }

                        Registration::create([
                            'user_id' => $userId,
                            'activity_id' => $activity->id,
                            'status' => $regStatus,
                            'is_waitlisted' => ($activity->capacity && $index >= $activity->capacity) && $regStatus !== RegistrationStatus::ABSENT_JUSTIFIED,
                            'justification' => $regStatus === RegistrationStatus::ABSENT_JUSTIFIED ? "Empêchement personnel" : null,
                            'registered_at' => $startTime->copy()->subDays(rand(1, 5)),
                        ]);

                        // If the activity is in the past, add attendance for registered users
                        if ($startTime->isPast() && $regStatus !== RegistrationStatus::ABSENT_JUSTIFIED) {
                            // Attendance status: 80% PRESENT, 10% LATE, 5% ABSENT, 5% EXCUSED
                            $attVal = rand(1, 100);
                            if ($attVal <= 80) {
                                $attStatus = AttendanceStatus::PRESENT;
                            } elseif ($attVal <= 90) {
                                $attStatus = AttendanceStatus::LATE;
                            } elseif ($attVal <= 95) {
                                $attStatus = AttendanceStatus::ABSENT;
                            } else {
                                $attStatus = AttendanceStatus::EXCUSED;
                            }

                            Attendance::create([
                                'user_id' => $userId,
                                'activity_id' => $activity->id,
                                'status' => $attStatus,
                                'scan_source' => rand(0, 1) ? 'manual' : 'qr_code',
                                'note' => $attStatus === AttendanceStatus::EXCUSED ? "Maladie / Voyage" : null,
                                'scanned_at' => $startTime->copy()->addMinutes(rand(-15, 30)),
                                'ip_address' => $faker->ipv4,
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('✅ Activités, inscriptions et pointages de test créés avec succès !');
    }
}
