<?php

namespace Tests\Feature;

use App\Events\GroupLeaderAssigned;
use App\Enums\UserStatus;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GroupManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user1;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create Admin user
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin@presentia.org',
            'password'   => bcrypt('Admin@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        // Create some regular users
        $this->user1 = User::create([
            'name'       => 'UserOne',
            'first_name' => 'John',
            'email'      => 'user1@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->user1->assignRole('Jeune');

        $this->user2 = User::create([
            'name'       => 'UserTwo',
            'first_name' => 'Jane',
            'email'      => 'user2@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->user2->assignRole('Jeune');
    }

    /**
     * Test admin can view groups index.
     */
    public function test_admin_can_view_group_list(): void
    {
        $group = Group::create([
            'name'        => 'Louveteaux',
            'category'    => 'Louvetisme',
            'description' => 'Groupe de jeunes louveteaux.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.groups.index'));

        $response->assertStatus(200);
        $response->assertSee('Louveteaux');
        $response->assertSee('Louvetisme');
    }

    /**
     * Test admin can create a new group.
     */
    public function test_admin_can_create_group(): void
    {
        Event::fake([GroupLeaderAssigned::class]);

        $response = $this->actingAs($this->admin)->post(route('admin.groups.store'), [
            'name'        => 'Éclaireurs',
            'category'    => 'Aînés',
            'description' => 'Groupe de scouts aînés.',
            'leader_id'   => $this->user1->id,
        ]);

        $group = Group::where('name', 'Éclaireurs')->first();
        $this->assertNotNull($group);
        $this->assertEquals($this->user1->id, $group->leader_id);

        $response->assertRedirect(route('admin.groups.show', $group));
        Event::assertDispatched(GroupLeaderAssigned::class, function ($event) use ($group) {
            return $event->group->id === $group->id && $event->newLeader->id === $this->user1->id;
        });
    }

    /**
     * Test admin can update a group.
     */
    public function test_admin_can_update_group(): void
    {
        Event::fake([GroupLeaderAssigned::class]);

        $group = Group::create([
            'name'        => 'Louveteaux',
            'category'    => 'Louvetisme',
            'description' => 'Groupe de jeunes louveteaux.',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.groups.update', $group), [
            'name'        => 'Louveteaux - Modifié',
            'category'    => 'Louvetisme',
            'description' => 'Groupe de jeunes louveteaux modifié.',
            'leader_id'   => $this->user2->id,
        ]);

        $group->refresh();
        $this->assertEquals('Louveteaux - Modifié', $group->name);
        $this->assertEquals($this->user2->id, $group->leader_id);

        $response->assertRedirect(route('admin.groups.show', $group));
        Event::assertDispatched(GroupLeaderAssigned::class);
    }

    /**
     * Test admin can soft delete a group.
     */
    public function test_admin_can_soft_delete_group(): void
    {
        $group = Group::create([
            'name' => 'Aînés',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.groups.destroy', $group));

        $response->assertRedirect(route('admin.groups.index'));
        $this->assertSoftDeleted('groups', ['id' => $group->id]);
    }

    /**
     * Test admin can assign a member to a group.
     */
    public function test_admin_can_assign_member_to_group(): void
    {
        $group = Group::create([
            'name' => 'Éclaireurs',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.groups.members.assign', $group), [
            'user_id' => $this->user1->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id'  => $this->user1->id,
            'left_at'  => null,
        ]);

        $this->assertEquals(1, $group->members()->wherePivotNull('left_at')->count());
    }

    /**
     * Test admin can remove a member from a group.
     */
    public function test_admin_can_remove_member_from_group(): void
    {
        $group = Group::create([
            'name' => 'Éclaireurs',
        ]);

        // Attach user
        $group->members()->attach($this->user1->id, [
            'joined_at' => now(),
            'left_at'   => null,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.groups.members.remove', [$group, $this->user1]));

        $response->assertRedirect();

        // Assert record is not deleted but left_at is populated
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id'  => $this->user1->id,
        ]);

        $pivot = $group->members()->first()->pivot;
        $this->assertNotNull($pivot->left_at);
        $this->assertEquals(0, $group->members()->wherePivotNull('left_at')->count());
    }

    /**
     * Test the role is auto-assigned when event is fired.
     */
    public function test_role_is_assigned_to_group_leader(): void
    {
        $group = Group::create([
            'name' => 'Éclaireurs',
        ]);

        $this->assertFalse($this->user1->hasRole('Chef de groupe'));

        // Assign leader through update
        $this->actingAs($this->admin)->put(route('admin.groups.update', $group), [
            'name'      => 'Éclaireurs',
            'leader_id' => $this->user1->id,
        ]);

        $this->user1->refresh();
        $this->assertTrue($this->user1->hasRole('Chef de groupe'));
    }

    /**
     * Test the role is revoked from the old group leader if they don't lead any other groups.
     */
    public function test_role_is_revoked_from_old_group_leader(): void
    {
        $group = Group::create([
            'name'      => 'Éclaireurs',
            'leader_id' => $this->user1->id,
        ]);

        $this->user1->refresh();
        $this->assertTrue($this->user1->hasRole('Chef de groupe'));

        // Assign leader to user2
        $this->actingAs($this->admin)->put(route('admin.groups.update', $group), [
            'name'      => 'Éclaireurs',
            'leader_id' => $this->user2->id,
        ]);

        $this->user1->refresh();
        $this->user2->refresh();

        $this->assertFalse($this->user1->hasRole('Chef de groupe'));
        $this->assertTrue($this->user2->hasRole('Chef de groupe'));
    }

    /**
     * Test that a group leader is automatically added as a member of the group.
     */
    public function test_group_leader_is_automatically_added_as_group_member(): void
    {
        $group = Group::create([
            'name'      => 'Éclaireurs',
            'leader_id' => $this->user1->id,
        ]);

        $this->assertTrue($group->members()->wherePivotNull('left_at')->where('users.id', $this->user1->id)->exists());
    }

    /**
     * Test group leader can view own group.
     */
    public function test_group_leader_can_view_own_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $response = $this->actingAs($chef)->get(route('admin.groups.show', $group));
        $response->assertStatus(200);
        $response->assertSee('Flambeaux');
    }

    /**
     * Test group leader cannot view other group.
     */
    public function test_group_leader_cannot_view_other_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $groupOwn = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $groupOther = Group::create([
            'name'      => 'Aînés',
            'leader_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($chef)->get(route('admin.groups.show', $groupOther));
        $response->assertStatus(403);
    }

    /**
     * Test group leader can update own group.
     */
    public function test_group_leader_can_update_own_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $response = $this->actingAs($chef)->put(route('admin.groups.update', $group), [
            'name'      => 'Flambeaux Modifié',
            'leader_id' => $chef->id,
        ]);

        $response->assertRedirect(route('admin.groups.show', $group));
        $group->refresh();
        $this->assertEquals('Flambeaux Modifié', $group->name);
    }

    /**
     * Test group leader cannot update other group.
     */
    public function test_group_leader_cannot_update_other_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $groupOther = Group::create([
            'name'      => 'Aînés',
            'leader_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($chef)->put(route('admin.groups.update', $groupOther), [
            'name'      => 'Aînés Modifié',
            'leader_id' => $this->admin->id,
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test group leader can assign a member to their own group.
     */
    public function test_group_leader_can_assign_member_to_own_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $response = $this->actingAs($chef)->post(route('admin.groups.members.assign', $group), [
            'user_id' => $this->user1->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id'  => $this->user1->id,
            'left_at'  => null,
        ]);
    }

    /**
     * Test group leader can remove a member from their own group.
     */
    public function test_group_leader_can_remove_member_from_own_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $group->members()->attach($this->user1->id, [
            'joined_at' => now(),
            'left_at'   => null,
        ]);

        $response = $this->actingAs($chef)->delete(route('admin.groups.members.remove', [$group, $this->user1]));

        $response->assertRedirect();
        $pivot = $group->members()->where('users.id', $this->user1->id)->first()->pivot;
        $this->assertNotNull($pivot->left_at);
    }

    /**
     * Test group leader cannot delete a group.
     */
    public function test_group_leader_cannot_delete_group(): void
    {
        $chef = User::create([
            'name'       => 'ChefOne',
            'first_name' => 'Paul',
            'email'      => 'chef@presentia.org',
            'password'   => bcrypt('Password123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $chef->assignRole('Chef de groupe');

        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $chef->id,
        ]);

        $response = $this->actingAs($chef)->delete(route('admin.groups.destroy', $group));
        $response->assertStatus(403);
    }

    /**
     * Test a Jeune user can view their own group details, but cannot edit the group.
     */
    public function test_jeune_can_view_own_group_details_but_cannot_edit(): void
    {
        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $this->admin->id,
        ]);

        // Attach user1 to the group
        $group->members()->attach($this->user1->id, [
            'joined_at' => now(),
            'left_at'   => null,
        ]);

        // 1. Jeune views own group page
        $response = $this->actingAs($this->user1)->get(route('admin.groups.show', $group));
        $response->assertStatus(200);
        $response->assertSee('Flambeaux');
        
        // Assert they don't see edit elements or forms
        $response->assertDontSee('Désigner un chef');
        $response->assertDontSee('Modifier');

        // 2. Jeune attempts to view edit page of own group
        $response = $this->actingAs($this->user1)->get(route('admin.groups.edit', $group));
        $response->assertStatus(403);

        // 3. Jeune attempts to update own group
        $response = $this->actingAs($this->user1)->put(route('admin.groups.update', $group), [
            'name' => 'New Name'
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test a Jeune user cannot assign or remove members.
     */
    public function test_jeune_cannot_assign_or_remove_members(): void
    {
        $group = Group::create([
            'name'      => 'Flambeaux',
            'leader_id' => $this->admin->id,
        ]);

        // Attach user1 to the group
        $group->members()->attach($this->user1->id, [
            'joined_at' => now(),
            'left_at'   => null,
        ]);

        // 1. Jeune attempts to assign a member
        $response = $this->actingAs($this->user1)->post(route('admin.groups.members.assign', $group), [
            'user_id' => $this->user2->id,
        ]);
        $response->assertStatus(403);

        // 2. Jeune attempts to remove a member
        $response = $this->actingAs($this->user1)->delete(route('admin.groups.members.remove', [$group, $this->user1]));
        $response->assertStatus(403);
    }
}
