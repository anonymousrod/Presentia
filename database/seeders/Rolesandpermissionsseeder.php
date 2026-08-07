<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset du cache Spatie avant de toucher aux permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ----------------------------------------------------------------
        // 1. CRÉATION DE TOUTES LES PERMISSIONS via PermissionEnum (source de vérité unique)
        // ----------------------------------------------------------------
        foreach (PermissionEnum::values() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // 2. CRÉATION DES 6 RÔLES + ATTRIBUTION DES PERMISSIONS PAR DÉFAUT
        // ----------------------------------------------------------------

        // Administrateur — bypass via Policy::before(), mais on lui donne tout
        $admin = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        $admin->code = 'admin';
        $admin->description = 'Super administrateur du système avec tous les accès.';
        $admin->is_system = true;
        $admin->save();
        $admin->syncPermissions(Permission::all());

        // Jeune — accès minimal : consulter, s'inscrire, scanner
        $jeune = Role::firstOrCreate(['name' => 'Jeune', 'guard_name' => 'web']);
        $jeune->code = 'default_user';
        $jeune->description = 'Membre de base de la jeunesse.';
        $jeune->is_system = true;
        $jeune->save();
        $jeune->syncPermissions([
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'group.view_own',
        ]);

        // Chef de groupe — gestion de son groupe uniquement
        $chef = Role::firstOrCreate(['name' => 'Chef de groupe', 'guard_name' => 'web']);
        $chef->code = 'group_leader';
        $chef->description = 'Responsable de la gestion et du suivi des membres de son groupe.';
        $chef->is_system = true;
        $chef->save();
        $chef->syncPermissions([
            'activity.view',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view_own',
            'attendance.validate_manual_own',
            'group.view_own',
            'stats.view_own_group',
            'notification.send_group',
            'finance.collect_own_group',
            'remittance.create',
        ]);

        // Chargé de collecte — gestion financière de son groupe
        $collecteur = Role::firstOrCreate(['name' => 'Chargé de collecte', 'guard_name' => 'web']);
        $collecteur->code = 'collector';
        $collecteur->description = 'Responsable de la collecte des cotisations au sein du groupe.';
        $collecteur->is_system = true;
        $collecteur->save();
        $collecteur->syncPermissions([
            'finance.collect_own_group',
            'remittance.create',
        ]);

        // Trésorier Général — Validation des versements
        $tresorier = Role::firstOrCreate(['name' => 'Trésorier Général', 'guard_name' => 'web']);
        $tresorier->code = 'treasurer';
        $tresorier->description = 'Gère les finances globales et valide les versements.';
        $tresorier->is_system = true;
        $tresorier->save();
        $tresorier->syncPermissions([
            'member.view',
            'group.view',
            'finance.view_all',
            'remittance.validate',
            'stats.view_global',
        ]);

        // Membre du bureau — vision globale, pas de gestion des comptes
        $bureau = Role::firstOrCreate(['name' => 'Membre du bureau', 'guard_name' => 'web']);
        $bureau->code = 'bureau_member';
        $bureau->description = 'Participe aux décisions et gère les activités globales.';
        $bureau->is_system = true;
        $bureau->save();
        $bureau->syncPermissions([
            'activity.view',
            'activity.create',
            'activity.edit',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view',
            'group.view',
            'member.view',
            'member.export',
            'stats.view_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
            'finance.view_all',
        ]);

        // Président — supervision globale + communication
        $president = Role::firstOrCreate(['name' => 'Président', 'guard_name' => 'web']);
        $president->code = 'president';
        $president->description = 'Dirige l\'organisation et supervise toutes les activités.';
        $president->is_system = true;
        $president->save();
        $president->syncPermissions([
            'activity.view',
            'activity.create',
            'activity.edit',
            'activity.edit',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view',
            'group.view',
            'member.view',
            'member.export',
            'stats.view_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
            'finance.view_all',
        ]);

        // Vice-président — permissions identiques au Président par défaut
        // (les différences sont gérées via permissions directes sur l'utilisateur)
        $vp = Role::firstOrCreate(['name' => 'Vice-président', 'guard_name' => 'web']);
        $vp->code = 'vice_president';
        $vp->is_system = true;
        $vp->save();
        $vp->syncPermissions([
            'activity.view',
            'activity.create',
            'activity.edit',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view',
            'group.view',
            'member.view',
            'stats.view_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
            'finance.view_all',
        ]);

        // Reset du cache après toutes les modifications
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ 8 rôles créés avec leurs permissions par défaut.');
        $this->command->info('   Permissions créées : ' . Permission::count());
    }
}
