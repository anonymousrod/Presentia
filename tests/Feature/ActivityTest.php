<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\Activity;
use App\Models\Registration;
use App\Enums\UserStatus;
use App\Enums\ActivityType;
use App\Enums\ActivityStatus;
use App\Enums\ActivityVisibility;
use App\Events\ActivityCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Group $group;
    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Admin User
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin@presentia.org',
            'password'   => bcrypt('Admin@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        // Member User
        $this->member = User::create([
            'name'       => 'Jeune',
            'first_name' => 'Simple',
            'email'      => 'jeune@presentia.org',
            'password'   => bcrypt('Jeune@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->member->assignRole('Jeune');

        // Target Group
        $this->group = Group::create([
            'name' => 'Target Group',
            'code' => 'TG01',
        ]);

        // Target Role
        $this->role = Role::firstOrCreate(['name' => 'Animateur']);
    }

    /**
     * Test admin can CRUD activities.
     */
    public function test_admin_can_crud_activities(): void
    {
        Event::fake();

        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.activities.index'));
        $response->assertStatus(200);

        // 2. Create Form
        $response = $this->actingAs($this->admin)->get(route('admin.activities.create'));
        $response->assertStatus(200);

        // 3. Store
        $response = $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'title' => 'New Activity',
            'description' => 'Test description',
            'type' => ActivityType::FORMATION->value,
            'status' => ActivityStatus::PUBLISHED->value,
            'visibility' => ActivityVisibility::ALL->value,
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 10,
        ]);

        $response->assertRedirect(route('admin.activities.index'));
        $this->assertDatabaseHas('activities', ['title' => 'New Activity']);

        // Verify Event was dispatched
        Event::assertDispatched(ActivityCreated::class);

        $activity = Activity::where('title', 'New Activity')->first();

        // 4. Show
        $response = $this->actingAs($this->admin)->get(route('admin.activities.show', $activity));
        $response->assertStatus(200);
        $response->assertSee('New Activity');

        // 5. Edit Form
        $response = $this->actingAs($this->admin)->get(route('admin.activities.edit', $activity));
        $response->assertStatus(200);

        // 6. Update
        $response = $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), [
            'title' => 'Updated Activity',
            'description' => 'Updated description',
            'type' => ActivityType::FORMATION->value,
            'status' => ActivityStatus::PUBLISHED->value,
            'visibility' => ActivityVisibility::ALL->value,
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 12,
        ]);

        $response->assertRedirect(route('admin.activities.index'));
        $this->assertDatabaseHas('activities', ['title' => 'Updated Activity']);

        // 7. Destroy (Soft Delete)
        $response = $this->actingAs($this->admin)->delete(route('admin.activities.destroy', $activity));
        $response->assertRedirect(route('admin.activities.index'));
        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    /**
     * Test validation rules for activities status and cancellation reason.
     */
    public function test_cancellation_reason_is_required_when_status_is_cancelled(): void
    {
        // 1. Create with status CANCELLED but no reason -> fails
        $response = $this->actingAs($this->admin)
            ->from(route('admin.activities.create'))
            ->post(route('admin.activities.store'), [
                'title' => 'Cancelled Activity',
                'description' => 'Test description',
                'type' => ActivityType::FORMATION->value,
                'status' => ActivityStatus::CANCELLED->value,
                'visibility' => ActivityVisibility::ALL->value,
                'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
                'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
                'cancellation_reason' => '',
            ]);

        $response->assertRedirect(route('admin.activities.create'));
        $response->assertSessionHasErrors(['cancellation_reason']);

        // 2. Create with status CANCELLED and reason -> passes
        $response = $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'title' => 'Cancelled Activity 2',
            'description' => 'Test description',
            'type' => ActivityType::FORMATION->value,
            'status' => ActivityStatus::CANCELLED->value,
            'visibility' => ActivityVisibility::ALL->value,
            'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'cancellation_reason' => 'Weather issues',
        ]);

        $response->assertRedirect(route('admin.activities.index'));
        $this->assertDatabaseHas('activities', [
            'title' => 'Cancelled Activity 2',
            'status' => ActivityStatus::CANCELLED->value,
            'cancellation_reason' => 'Weather issues'
        ]);
    }

    /**
     * Test archiving command transitions past activities.
     */
    public function test_archive_old_activities_command(): void
    {
        // Activity in the past (ended 95 days ago)
        $pastActivity = Activity::create([
            'title' => 'Past Activity',
            'description' => 'Test description',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->subDays(96),
            'end_time' => now()->subDays(95),
        ]);

        // Future activity
        $futureActivity = Activity::create([
            'title' => 'Future Activity',
            'description' => 'Test description',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        // Run the console command
        Artisan::call('activities:archive-old');

        // Check database status
        $this->assertEquals(ActivityStatus::ARCHIVED, $pastActivity->fresh()->status);
        $this->assertEquals(ActivityStatus::PUBLISHED, $futureActivity->fresh()->status);
    }

    /**
     * Test youth frontend visibility filters.
     */
    public function test_youth_frontend_visibility_filters(): void
    {
        // 1. ALL visibility
        $actAll = Activity::create([
            'title' => 'All Vis Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        // 2. GROUP visibility (not member)
        $actGroup = Activity::create([
            'title' => 'Group Vis Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::GROUP,
            'visibility_group_id' => $this->group->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        // 3. ROLE visibility (not holding)
        $actRole = Activity::create([
            'title' => 'Role Vis Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ROLE,
            'visibility_role_id' => $this->role->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        // Access frontend list
        $response = $this->actingAs($this->member)->get(route('activities.index'));
        $response->assertStatus(200);
        $response->assertSee('All Vis Activity');
        $response->assertDontSee('Group Vis Activity');
        $response->assertDontSee('Role Vis Activity');

        // Make member join target group
        $this->member->groups()->attach($this->group->id, ['joined_at' => now()]);

        // Access frontend list again
        $response = $this->actingAs($this->member)->get(route('activities.index'));
        $response->assertSee('Group Vis Activity');
        $response->assertDontSee('Role Vis Activity');

        // Make member hold role
        $this->member->assignRole($this->role);

        // Access frontend list again
        $response = $this->actingAs($this->member)->get(route('activities.index'));
        $response->assertSee('Role Vis Activity');
    }

    /**
     * Test registration, capacity limits, waitlist promotion, and cancellation.
     */
    public function test_registration_and_waitlist_handling(): void
    {
        // Activity with capacity limit of 1
        $activity = Activity::create([
            'title' => 'Limited Capacity Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'capacity' => 1,
        ]);

        // 1. Member 1 registers
        $response = $this->actingAs($this->member)->post(route('activities.register', $activity), [
            'status' => 'PRESENT',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'is_waitlisted' => false,
            'status' => 'PRESENT',
        ]);

        // 2. Member 2 registers (should be waitlisted)
        $member2 = User::create([
            'name'       => 'Jeune2',
            'first_name' => 'Simple2',
            'email'      => 'jeune2@presentia.org',
            'password'   => bcrypt('Jeune@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $member2->assignRole('Jeune');

        $response = $this->actingAs($member2)->post(route('activities.register', $activity), [
            'status' => 'PRESENT',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('registrations', [
            'user_id' => $member2->id,
            'activity_id' => $activity->id,
            'is_waitlisted' => true,
        ]);

        // 3. Member 1 cancels (member 2 should be promoted)
        $response = $this->actingAs($this->member)->post(route('activities.unregister', $activity), [
            'justification' => 'Contretemps imprévu',
        ]);
        $response->assertRedirect();

        // Member 1 registration becomes ABSENT_JUSTIFIED
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'ABSENT_JUSTIFIED',
            'justification' => 'Contretemps imprévu',
        ]);

        // Member 2 registration becomes is_waitlisted = false
        $this->assertDatabaseHas('registrations', [
            'user_id' => $member2->id,
            'activity_id' => $activity->id,
            'is_waitlisted' => false,
        ]);
    }

    /**
     * Test that users cannot register or unregister if the activity has started.
     */
    public function test_cannot_register_or_unregister_when_activity_has_started(): void
    {
        $activity = Activity::create([
            'title' => 'Started Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->subMinutes(10), // Déjà commencé
            'end_time' => now()->addHour(),
        ]);

        // 1. Tenter de s'inscrire
        $response = $this->actingAs($this->member)->post(route('activities.register', $activity), [
            'status' => 'PRESENT',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error', "Cette activité a déjà commencé. L'inscription n'est plus possible.");
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
        ]);

        // 2. Créer une inscription manuellement pour tester la désinscription
        Registration::create([
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        // Tenter de se désinscrire
        $response = $this->actingAs($this->member)->post(route('activities.unregister', $activity), [
            'justification' => 'Contretemps imprévu',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error', "Cette activité a déjà commencé. La désinscription n'est plus possible.");

        // L'inscription doit toujours être active (status != ABSENT_JUSTIFIED)
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
        ]);
    }

    /**
     * Test validation rules for unregistration justification.
     */
    public function test_member_cannot_unregister_without_justification(): void
    {
        $activity = Activity::create([
            'title' => 'Future Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        // Create registration
        Registration::create([
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        // 1. Unregister with no justification -> fails
        $response = $this->actingAs($this->member)
            ->post(route('activities.unregister', $activity), []);
        $response->assertSessionHasErrors(['justification']);

        // 2. Unregister with too short justification -> fails
        $response = $this->actingAs($this->member)
            ->post(route('activities.unregister', $activity), [
                'justification' => 'abc', // too short
            ]);
        $response->assertSessionHasErrors(['justification']);

        // 3. Unregister with valid justification -> passes
        $response = $this->actingAs($this->member)
            ->post(route('activities.unregister', $activity), [
                'justification' => 'Contretemps de dernière minute',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'ABSENT_JUSTIFIED',
            'justification' => 'Contretemps de dernière minute',
        ]);
    }

    /**
     * Test registration with UNCERTAIN status.
     */
    public function test_registration_with_uncertain_status(): void
    {
        $activity = Activity::create([
            'title' => 'Future Activity 2',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        $response = $this->actingAs($this->member)->post(route('activities.register', $activity), [
            'status' => 'UNCERTAIN',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
            'status' => 'UNCERTAIN',
        ]);
    }

    /**
     * Test registration fails if activity is starting in less than 2 hours.
     */
    public function test_cannot_register_less_than_2_hours_before_start(): void
    {
        $activity = Activity::create([
            'title' => 'Near Start Activity',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addMinutes(90), // 1h30 from now
            'end_time' => now()->addHours(3),
        ]);

        $response = $this->actingAs($this->member)->post(route('activities.register', $activity), [
            'status' => 'PRESENT',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->member->id,
            'activity_id' => $activity->id,
        ]);
    }

    /**
     * Test job SendRegistrationConfirmation is dispatched.
     */
    public function test_send_registration_confirmation_job_is_dispatched(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $activity = Activity::create([
            'title' => 'Future Activity 3',
            'type' => ActivityType::FORMATION,
            'status' => ActivityStatus::PUBLISHED,
            'visibility' => ActivityVisibility::ALL,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ]);

        $response = $this->actingAs($this->member)->post(route('activities.register', $activity), [
            'status' => 'PRESENT',
        ]);

        $response->assertRedirect();
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendRegistrationConfirmation::class);
    }
}
