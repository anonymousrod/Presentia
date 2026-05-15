<?php

namespace Database\Seeders;

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
        // 1. CRÉATION DE TOUTES LES PERMISSIONS (format resource.action)
        // ----------------------------------------------------------------
        $permissions = [
            // Membres
            'member.view',
            'member.create',
            'member.edit',
            'member.delete',
            'member.restore',
            'member.export',

            // Groupes
            'group.view',
            'group.view_own',       // Chef : son groupe uniquement
            'group.create',
            'group.edit',
            'group.delete',
            'group.assign_member',

            // Activités
            'activity.view',
            'activity.create',
            'activity.edit',
            'activity.publish',
            'activity.cancel',
            'activity.archive',

            // Présences
            'attendance.view',
            'attendance.view_own',  // Chef : son groupe uniquement
            'attendance.validate_manual',
            'attendance.scan_qr',

            // Inscriptions
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',

            // Notifications
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',

            // Statistiques & rapports
            'stats.view_global',
            'stats.view_own_group',
            'report.export_global',
            'report.export_own_group',

            // Rôles & permissions
            'role.manage',
            'permission.manage',

            // Audit
            'audit.view',

            // QR Code
            'qrcode.generate',
            'qrcode.revoke',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // 2. CRÉATION DES 6 RÔLES + ATTRIBUTION DES PERMISSIONS PAR DÉFAUT
        // ----------------------------------------------------------------

        // Administrateur — bypass via Policy::before(), mais on lui donne tout
        $admin = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Jeune — accès minimal : consulter, s'inscrire, scanner
        $jeune = Role::firstOrCreate(['name' => 'Jeune', 'guard_name' => 'web']);
        $jeune->syncPermissions([
            'activity.view',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
        ]);

        // Chef de groupe — gestion de son groupe uniquement
        $chef = Role::firstOrCreate(['name' => 'Chef de groupe', 'guard_name' => 'web']);
        $chef->syncPermissions([
            'activity.view',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view_own',
            'attendance.validate_manual',
            'group.view_own',
            'stats.view_own_group',
            'report.export_own_group',
            'notification.send_group',
        ]);

        // Membre du bureau — vision globale, pas de gestion des comptes
        $bureau = Role::firstOrCreate(['name' => 'Membre du bureau', 'guard_name' => 'web']);
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
            'report.export_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
        ]);

        // Président — supervision globale + communication
        $president = Role::firstOrCreate(['name' => 'Président', 'guard_name' => 'web']);
        $president->syncPermissions([
            'activity.view',
            'activity.create',
            'activity.edit',
            'activity.publish',
            'activity.cancel',
            'registration.create',
            'registration.edit_own',
            'registration.cancel_own',
            'attendance.scan_qr',
            'attendance.view',
            'group.view',
            'member.view',
            'member.export',
            'stats.view_global',
            'report.export_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
        ]);

        // Vice-président — permissions identiques au Président par défaut
        // (les différences sont gérées via permissions directes sur l'utilisateur)
        $vp = Role::firstOrCreate(['name' => 'Vice-président', 'guard_name' => 'web']);
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
            'report.export_global',
            'notification.send_all',
            'notification.send_group',
            'notification.send_role',
            'notification.send_individual',
        ]);

        // Reset du cache après toutes les modifications
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ 6 rôles créés avec leurs permissions par défaut.');
        $this->command->info('   Permissions créées : ' . Permission::count());
    }
}