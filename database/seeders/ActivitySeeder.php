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

        $this->command->info('✅ Activités, inscriptions et pointages de test créés avec succès !');
    }
}
