<?php

namespace Tests\Feature;

use App\Enums\PermissionEnum;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RBACTest extends TestCase
{
    use RefreshDatabase;

    private User $userWithoutRole;
    private User $jeune;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialiser le seeder des rôles et permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Utilisateur sans aucun rôle
        $this->userWithoutRole = User::create([
            'name'       => 'Inconnu',
            'first_name' => 'Utilisateur',
            'email'      => 'norole@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);

        // Utilisateur avec le rôle "Jeune"
        $this->jeune = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jeune',
            'email'      => 'jeune@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->jeune->assignRole('Jeune');

        // Administrateur
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin@presentia.org',
            'password'   => bcrypt('Admin@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');
    }

    /**
     * Test que PermissionEnum::values() retourne au moins 20 permissions.
     */
    public function test_permission_enum_has_at_least_twenty_permissions(): void
    {
        $values = PermissionEnum::values();
        $this->assertGreaterThanOrEqual(20, count($values));
    }

    /**
     * Test que chaque cas de PermissionEnum suit le format 'resource.action'.
     */
    public function test_all_permission_enum_values_follow_resource_action_format(): void
    {
        foreach (PermissionEnum::cases() as $permission) {
            $this->assertMatchesRegularExpression(
                '/^[a-z_]+\.[a-z_]+$/',
                $permission->value,
                "La permission '{$permission->value}' ne respecte pas le format 'resource.action'"
            );
        }
    }

    /**
     * Test qu'un utilisateur sans rôle ne peut pas effectuer d'actions.
     */
    public function test_user_without_role_is_denied_all_permissions(): void
    {
        $this->actingAs($this->userWithoutRole);

        $this->assertFalse($this->userWithoutRole->can(PermissionEnum::MEMBER_VIEW->value));
        $this->assertFalse($this->userWithoutRole->can(PermissionEnum::ACTIVITY_CREATE->value));
        $this->assertFalse($this->userWithoutRole->can(PermissionEnum::AUDIT_VIEW->value));
    }

    /**
     * Test que l'ajout d'une permission directe accorde l'accès SANS changer le rôle.
     */
    public function test_direct_permission_grants_access_without_changing_role(): void
    {
        // Au départ : le Jeune ne peut pas voir les membres
        $this->assertFalse($this->jeune->can(PermissionEnum::MEMBER_VIEW->value));
        $this->assertTrue($this->jeune->hasRole('Jeune'));

        // Ajout de permission directe sur l'utilisateur (sans changer le rôle)
        $this->jeune->givePermissionTo(PermissionEnum::MEMBER_VIEW->value);

        // Rafraîchir le cache Spatie
        $this->jeune->refresh();

        // Maintenant il peut voir les membres
        $this->assertTrue($this->jeune->can(PermissionEnum::MEMBER_VIEW->value));

        // Et il est toujours "Jeune", le rôle n'a pas changé
        $this->assertTrue($this->jeune->hasRole('Jeune'));
    }

    /**
     * Test que le retrait d'un rôle supprime les permissions du rôle mais conserve les directes.
     */
    public function test_removing_role_keeps_direct_permissions_but_removes_role_permissions(): void
    {
        // Donner une permission directe au Jeune
        $this->jeune->givePermissionTo(PermissionEnum::MEMBER_VIEW->value);
        $this->jeune->refresh();

        // Vérifier avant le retrait du rôle
        $this->assertTrue($this->jeune->can(PermissionEnum::ACTIVITY_VIEW->value)); // Permission du rôle Jeune
        $this->assertTrue($this->jeune->can(PermissionEnum::MEMBER_VIEW->value)); // Permission directe

        // Retirer le rôle
        $this->jeune->removeRole('Jeune');
        $this->jeune->refresh();

        // La permission du rôle est perdue
        $this->assertFalse($this->jeune->can(PermissionEnum::ACTIVITY_VIEW->value));

        // Mais la permission directe est conservée !
        $this->assertTrue($this->jeune->can(PermissionEnum::MEMBER_VIEW->value));
    }

    /**
     * Test que les 6 rôles attendus ont été seedés correctement.
     */
    public function test_six_expected_roles_are_seeded(): void
    {
        $expectedRoles = [
            'Administrateur',
            'Jeune',
            'Chef de groupe',
            'Membre du bureau',
            'Président',
            'Vice-président',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertNotNull(
                Role::where('name', $roleName)->first(),
                "Le rôle '{$roleName}' est manquant dans la base de données."
            );
        }
    }

    /**
     * Test que toutes les permissions du seeder sont bien créées en DB.
     */
    public function test_all_permission_enum_permissions_are_seeded_in_database(): void
    {
        foreach (PermissionEnum::values() as $permissionValue) {
            $this->assertNotNull(
                Permission::where('name', $permissionValue)->first(),
                "La permission '{$permissionValue}' est manquante en base de données."
            );
        }
    }

    /**
     * Test que l'Administrateur possède toutes les permissions de l'application.
     */
    public function test_admin_has_all_permissions(): void
    {
        foreach (PermissionEnum::values() as $permissionValue) {
            $this->assertTrue(
                $this->admin->hasPermissionTo($permissionValue),
                "L'Administrateur ne possède pas la permission '{$permissionValue}'."
            );
        }
    }

    /**
     * Test que le Jeune possède uniquement ses permissions de base.
     */
    public function test_jeune_has_only_minimal_permissions(): void
    {
        // Le Jeune peut faire ceci
        $this->assertTrue($this->jeune->can(PermissionEnum::ACTIVITY_VIEW->value));
        $this->assertTrue($this->jeune->can(PermissionEnum::REGISTRATION_CREATE->value));
        $this->assertTrue($this->jeune->can(PermissionEnum::ATTENDANCE_SCAN_QR->value));

        // Le Jeune ne peut PAS faire cela
        $this->assertFalse($this->jeune->can(PermissionEnum::MEMBER_VIEW->value));
        $this->assertFalse($this->jeune->can(PermissionEnum::ACTIVITY_CREATE->value));
        $this->assertFalse($this->jeune->can(PermissionEnum::AUDIT_VIEW->value));
        $this->assertFalse($this->jeune->can(PermissionEnum::PERMISSION_MANAGE->value));
    }
}
