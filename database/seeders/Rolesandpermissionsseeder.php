<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset du cache Spatie avant de toucher aux permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. CRÉATION DE TOUTES LES PERMISSIONS SYSTÈME (GLOBALES)
        foreach (PermissionEnum::values() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. CRÉATION DU RÔLE SUPER ADMIN (GLOBAL SANS CHURCH_ID)
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web', 'church_id' => null],
            [
                'code'        => 'super_admin',
                'description' => 'Super administrateur de la plateforme SaaS avec gestion de toutes les églises et abonnements.',
                'is_system'   => true,
            ]
        );
        $superAdmin->syncPermissions(Permission::all());

        // 3. SEED DES RÔLES POUR L'ÉGLISE PAR DÉFAUT (ID 1 : Éber)
        self::seedRolesForChurch(1);
    }

    /**
     * Génère l'ensemble complet des rôles et de leurs permissions par défaut pour une église donnée.
     */
    public static function seedRolesForChurch(int $churchId): void
    {
        setPermissionsTeamId($churchId);

        $defaultRoles = [
            [
                'name'        => 'Administrateur',
                'code'        => 'admin',
                'description' => 'Administrateur principal de l\'église.',
                'is_system'   => true,
                'permissions' => Permission::pluck('name')->toArray(),
            ],
            [
                'name'        => 'Jeune',
                'code'        => 'default_user',
                'description' => 'Membre de base de la jeunesse.',
                'is_system'   => true,
                'permissions' => [
                    'attendance.scan_qr',
                    'group.view_own',
                    'registration.cancel_own',
                    'registration.create',
                    'registration.edit_own',
                ],
            ],
            [
                'name'        => 'Chef de groupe',
                'code'        => 'group_leader',
                'description' => 'Responsable de la gestion et du suivi des membres de son groupe.',
                'is_system'   => true,
                'permissions' => [
                    'attendance.scan_qr',
                    'attendance.validate_manual_own',
                    'attendance.view_own',
                    'group.view_own',
                    'registration.cancel_own',
                    'registration.create',
                    'registration.edit_own',
                    'remittance.create',
                    'stats.view_own_group',
                ],
            ],
            [
                'name'        => 'Chargé de collecte',
                'code'        => 'collector',
                'description' => 'Responsable de la collecte des cotisations au sein du groupe.',
                'is_system'   => true,
                'permissions' => [
                    'finance.collect_own_group',
                    'remittance.create',
                ],
            ],
            [
                'name'        => 'Trésorier Général',
                'code'        => 'treasurer',
                'description' => 'Gère les finances globales et valide les versements.',
                'is_system'   => true,
                'permissions' => [
                    'finance.collect_own_group',
                    'finance.view_all',
                    'group.view_own',
                    'remittance.create',
                    'remittance.validate',
                    'stats.view_global',
                ],
            ],
            [
                'name'        => 'Membre du bureau',
                'code'        => 'bureau_member',
                'description' => 'Participe aux décisions et gère les activités globales.',
                'is_system'   => true,
                'permissions' => [
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
                ],
            ],
            [
                'name'        => 'Président',
                'code'        => 'president',
                'description' => 'Dirige l\'organisation et supervise toutes les activités.',
                'is_system'   => true,
                'permissions' => [
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
                ],
            ],
            [
                'name'        => 'Vice-président',
                'code'        => 'vice_president',
                'description' => 'Assiste le président dans la supervision des activités.',
                'is_system'   => true,
                'permissions' => [
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
                ],
            ],
        ];

        foreach ($defaultRoles as $rData) {
            $role = Role::firstOrCreate(
                [
                    'church_id'  => $churchId,
                    'name'       => $rData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'code'        => $rData['code'],
                    'description' => $rData['description'],
                    'is_system'   => $rData['is_system'],
                ]
            );

            // Synchroniser les permissions du rôle
            $role->syncPermissions($rData['permissions']);
        }
    }
}
