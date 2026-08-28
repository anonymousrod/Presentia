<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Group;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Contribution;
use App\Models\Remittance;
use App\Models\AppSetting;
use App\Models\Gallery;
use App\Models\Attendance;
use App\Models\Registration;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création ou récupération de l'église par défaut (Église Baptiste Éber)
        $church = Church::firstOrCreate(
            ['slug' => 'eber'],
            [
                'name'                   => 'Église Baptiste Éber',
                'code'                   => 'EBER-001',
                'email'                  => 'contact@eber.org',
                'phone'                  => '+229 69 12 90 89',
                'address'                => 'Cotonou, Bénin',
                'city'                   => 'Cotonou',
                'status'                 => 'active',
                'subscription_starts_at' => Carbon::now(),
                'subscription_expires_at' => Carbon::now()->addYear(), // Abonnement d'un an
                'subscription_amount'    => 150000,
                'subscription_plan'      => 'Annuel (1 an)',
                'notes'                  => 'Église principale originelle de la plateforme.',
            ]
        );

        // 2. Création de l'enregistrement de l'abonnement 1 An initial
        Subscription::firstOrCreate(
            [
                'church_id' => $church->id,
                'plan_name' => 'Abonnement Annuel (1 an)',
            ],
            [
                'starts_at'         => Carbon::now(),
                'expires_at'        => Carbon::now()->addYear(),
                'amount'            => 150000,
                'payment_method'    => 'Initialisation Système',
                'payment_reference' => 'SYS-INIT-' . Carbon::now()->format('Ymd'),
                'status'            => 'active',
                'notes'             => 'Premier abonnement annuel d\'initialisation.',
            ]
        );

        // 3. Attribution des données existantes non scopées à cette église par défaut
        $tenantModels = [
            User::class,
            Group::class,
            Activity::class,
            ActivityType::class,
            Contribution::class,
            Remittance::class,
            AppSetting::class,
            Gallery::class,
            Attendance::class,
            Registration::class,
            AuditLog::class,
        ];

        foreach ($tenantModels as $modelClass) {
            $modelClass::withoutGlobalScopes()->whereNull('church_id')->update(['church_id' => $church->id]);
        }

        // 4. Création / Mise à jour du Super Administrateur de la plateforme
        $admin = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'admin@eber.org'],
            [
                'name'       => 'Administrateur',
                'first_name' => 'ÉBER',
                'phone'      => '69129089',
                'password'   => Hash::make('Admin@1234!'),
                'status'     => 'ACTIVE',
                'birth_date' => null,
                'church_id'  => $church->id,
            ]
        );

        $admin->church_id = $church->id;
        $admin->save();

        setPermissionsTeamId($church->id);

        // Attribuer les rôles Super Admin et Administrateur
        if (! $admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
        if (! $admin->hasRole('Administrateur')) {
            $admin->assignRole('Administrateur');
        }

        $this->command->info('✅ Église par défaut "Église Baptiste Éber" configurée avec abonnement 1 an.');
        $this->command->info('👑 Compte Super Administrateur SaaS configuré : admin@eber.org');
    }
}
