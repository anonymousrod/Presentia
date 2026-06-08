<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Enums\UserStatus;
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $nonAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        $this->nonAdmin = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $this->nonAdmin->assignRole('Jeune');
    }

    /** @test */
    public function admin_can_access_roles_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    /** @test */
    public function non_admin_is_denied_access_to_roles_index()
    {
        $response = $this->actingAs($this->nonAdmin)->get(route('admin.roles.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_a_role()
    {
        $roleData = [
            'name' => 'New Role',
            'permissions' => [
                PermissionEnum::MEMBER_VIEW->value,
                PermissionEnum::ACTIVITY_VIEW->value,
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.roles.store'), $roleData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'New Role']);

        $role = Role::findByName('New Role');
        $this->assertTrue($role->hasPermissionTo(PermissionEnum::MEMBER_VIEW->value));
    }

    /** @test */
    public function admin_can_update_a_role()
    {
        $role = Role::create(['name' => 'Existing Role']);

        $updateData = [
            'name' => 'Updated Role Name',
            'permissions' => [
                PermissionEnum::MEMBER_CREATE->value,
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.roles.update', $role), $updateData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Updated Role Name']);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo(PermissionEnum::MEMBER_CREATE->value));
        $this->assertFalse($role->hasPermissionTo(PermissionEnum::MEMBER_VIEW->value));
    }

    /** @test */
    public function admin_cannot_delete_system_roles()
    {
        $adminRole = Role::findByName('Administrateur');

        $response = $this->actingAs($this->admin)->delete(route('admin.roles.destroy', $adminRole));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'Administrateur']);
    }

    /** @test */
    public function admin_can_delete_a_custom_role()
    {
        $role = Role::create(['name' => 'Custom Role']);

        $response = $this->actingAs($this->admin)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['name' => 'Custom Role']);
    }

    /** @test */
    public function admin_can_access_user_permissions_edit_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.users.permissions.edit', $user));

        $response->assertStatus(200);
        $response->assertViewHas('user', $user);
        $response->assertViewHas('roles');
        $response->assertViewHas('groupedPermissions');
    }

    /** @test */
    public function admin_can_update_user_roles_and_direct_permissions()
    {
        $user = User::factory()->create();
        $role = Role::findByName('Jeune');

        $updateData = [
            'roles' => ['Jeune'],
            'permissions' => [PermissionEnum::AUDIT_VIEW->value],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.permissions.update', $user), $updateData);

        $response->assertRedirect(route('admin.users.permissions.edit', $user));

        $user->refresh();
        $this->assertTrue($user->hasRole('Jeune'));
        $this->assertTrue($user->hasDirectPermission(PermissionEnum::AUDIT_VIEW->value));
    }

    /** @test */
    public function service_clears_spatie_cache_after_modifications()
    {
        $service = app(\App\Services\PermissionService::class);

        // 1. Seed the cache manually
        cache()->put('spatie.permission.cache', 'dummy-data');
        $this->assertEquals('dummy-data', cache()->get('spatie.permission.cache'));

        // 2. Test createRole clears cache
        $service->createRole('Temp Role', []);
        $this->assertNull(cache()->get('spatie.permission.cache'));

        // 3. Seed cache again
        cache()->put('spatie.permission.cache', 'dummy-data');

        // 4. Test updateRole clears cache
        $role = Role::findByName('Temp Role');
        $service->updateRole($role, 'Temp Role Updated', []);
        $this->assertNull(cache()->get('spatie.permission.cache'));

        // 5. Seed cache again
        cache()->put('spatie.permission.cache', 'dummy-data');

        // 6. Test deleteRole clears cache
        $service->deleteRole($role);
        $this->assertNull(cache()->get('spatie.permission.cache'));
    }
}



