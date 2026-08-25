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
        // 1. CRÉATION DE TOUTES LES PERMISSIONS via PermissionEnum
        // ----------------------------------------------------------------
        foreach (PermissionEnum::values() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // 2. CRÉATION ET CONFORMITÉ DES RÔLES ET PERMISSIONS DE LA BD
        // ----------------------------------------------------------------

        // 1. Administrateur — Tous les accès
        $admin = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        $admin->code = 'admin';
        $admin->description = 'Super administrateur du système avec tous les accès.';
        $admin->is_system = true;
        $admin->save();
        $admin->syncPermissions(Permission::all());

        // 2. Jeune
        $jeune = Role::firstOrCreate(['name' => 'Jeune', 'guard_name' => 'web']);
        $jeune->code = 'default_user';
        $jeune->description = 'Membre de base de la jeunesse.';
        $jeune->is_system = true;
        $jeune->save();
        $jeune->syncPermissions([
            'attendance.scan_qr',
            'group.view_own',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
        ]);

        // 3. Chef de groupe
        $chef = Role::firstOrCreate(['name' => 'Chef de groupe', 'guard_name' => 'web']);
        $chef->code = 'group_leader';
        $chef->description = 'Responsable de la gestion et du suivi des membres de son groupe.';
        $chef->is_system = true;
        $chef->save();
        $chef->syncPermissions([
            'attendance.scan_qr',
            'attendance.validate_manual_own',
            'attendance.view_own',
            'group.view_own',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
            'remittance.create',
            'stats.view_own_group',
        ]);

        // 4. Chargé de collecte
        $collecteur = Role::firstOrCreate(['name' => 'Chargé de collecte', 'guard_name' => 'web']);
        $collecteur->code = 'collector';
        $collecteur->description = 'Responsable de la collecte des cotisations au sein du groupe.';
        $collecteur->is_system = true;
        $collecteur->save();
        $collecteur->syncPermissions([
            'finance.collect_own_group',
            'remittance.create',
        ]);

        // 5. Trésorier Général
        $tresorier = Role::firstOrCreate(['name' => 'Trésorier Général', 'guard_name' => 'web']);
        $tresorier->code = 'treasurer';
        $tresorier->description = 'Gère les finances globales et valide les versements.';
        $tresorier->is_system = true;
        $tresorier->save();
        $tresorier->syncPermissions([
            'finance.collect_own_group',
            'finance.view_all',
            'group.view_own',
            'remittance.create',
            'remittance.validate',
            'stats.view_global',
        ]);

        // 6. Membre du bureau
        $bureau = Role::firstOrCreate(['name' => 'Membre du bureau', 'guard_name' => 'web']);
        $bureau->code = 'bureau_member';
        $bureau->description = 'Participe aux décisions et gère les activités globales.';
        $bureau->is_system = true;
        $bureau->save();
        $bureau->syncPermissions([
            'activity.create',
            'activity.edit',
            'activity.view',
            'attendance.scan_qr',
            'attendance.view',
            'finance.view_all',
            'group.view',
            'member.export',
            'member.view',
            'notification.send_all',
            'notification.send_group',
            'notification.send_individual',
            'notification.send_role',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
            'stats.view_global',
        ]);

        // 7. Président
        $president = Role::firstOrCreate(['name' => 'Président', 'guard_name' => 'web']);
        $president->code = 'president';
        $president->description = 'Dirige l\'organisation et supervise toutes les activités.';
        $president->is_system = true;
        $president->save();
        $president->syncPermissions([
            'activity.create',
            'activity.edit',
            'activity.view',
            'attendance.scan_qr',
            'attendance.view',
            'finance.view_all',
            'group.view',
            'member.export',
            'member.view',
            'notification.send_all',
            'notification.send_group',
            'notification.send_individual',
            'notification.send_role',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
            'stats.view_global',
        ]);

        // 8. Vice-président
        $vp = Role::firstOrCreate(['name' => 'Vice-président', 'guard_name' => 'web']);
        $vp->code = 'vice_president';
        $vp->description = 'Assiste le président dans la supervision des activités.';
        $vp->is_system = true;
        $vp->save();
        $vp->syncPermissions([
            'activity.create',
            'activity.edit',
            'activity.view',
            'attendance.scan_qr',
            'attendance.view',
            'finance.view_all',
            'group.view',
            'member.view',
            'notification.send_all',
            'notification.send_group',
            'notification.send_individual',
            'notification.send_role',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
            'stats.view_global',
        ]);

        // 9. Rôle personnalisé "Exauce" (présent dans la BD)
        $exauce = Role::firstOrCreate(['name' => 'Exauce', 'guard_name' => 'web']);
        $exauce->code = null;
        $exauce->description = 'texte';
        $exauce->is_system = false;
        $exauce->save();
        $exauce->syncPermissions([
            'activity.create',
            'activity.edit',
            'activity.view',
            'audit.view',
            'notification.send_all',
            'notification.send_group',
            'notification.send_individual',
            'notification.send_role',
            'registration.cancel_own',
            'registration.create',
            'registration.edit_own',
            'stats.view_global',
            'stats.view_own_group',
        ]);

        // Reset du cache après toutes les modifications
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Rôles et permissions synchronisés avec succès et conformes à la base de données.');
        $this->command->info('   Total rôles : ' . Role::count());
        $this->command->info('   Total permissions : ' . Permission::count());
    }
}
