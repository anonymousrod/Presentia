<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\User;
use App\Policies\GroupPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ReportPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $jeune;
    private User $chef;
    private User $bureau;
    private Group $groupA;
    private Group $groupB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Admin
        $this->admin = User::create([
            'name' => 'Admin', 'first_name' => 'System',
            'email' => 'admin@presentia.org', 'password' => bcrypt('Admin@1234!'),
            'status' => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        // Jeune
        $this->jeune = User::create([
            'name' => 'Dupont', 'first_name' => 'Jean',
            'email' => 'jeune@presentia.org', 'password' => bcrypt('Jeune@1234!'),
            'status' => UserStatus::ACTIVE,
        ]);
        $this->jeune->assignRole('Jeune');

        // Chef de groupe
        $this->chef = User::create([
            'name' => 'Martin', 'first_name' => 'Paul',
            'email' => 'chef@presentia.org', 'password' => bcrypt('Chef@1234!'),
            'status' => UserStatus::ACTIVE,
        ]);
        $this->chef->assignRole('Chef de groupe');

        // Membre du bureau
        $this->bureau = User::create([
            'name' => 'Bureau', 'first_name' => 'Marie',
            'email' => 'bureau@presentia.org', 'password' => bcrypt('Bureau@1234!'),
            'status' => UserStatus::ACTIVE,
        ]);
        $this->bureau->assignRole('Membre du bureau');

        // Groupe A dirigé par le chef
        $this->groupA = Group::create([
            'name' => 'Groupe Alpha', 'description' => 'Premier groupe',
            'category' => 'Test', 'leader_id' => $this->chef->id,
        ]);

        // Groupe B sans lien avec le chef
        $this->groupB = Group::create([
            'name' => 'Groupe Beta', 'description' => 'Deuxième groupe',
            'category' => 'Test', 'leader_id' => $this->bureau->id,
        ]);

        // Ajouter le jeune comme membre du groupe A
        $this->groupA->members()->attach($this->jeune->id, ['joined_at' => now()]);
    }

    // ================================================================
    // USER POLICY
    // ================================================================

    /**
     * Admin peut modifier le phone d'un autre utilisateur.
     */
    public function test_admin_can_update_phone_of_any_user(): void
    {
        $this->assertTrue($this->admin->can('updatePhone', $this->jeune));
    }

    /**
     * Jeune NE PEUT PAS modifier le phone d'un autre utilisateur (même le sien via updatePhone).
     */
    public function test_jeune_cannot_update_phone(): void
    {
        $this->assertFalse($this->jeune->can('updatePhone', $this->jeune));
    }

    /**
     * Jeune peut modifier son propre profil (hors phone).
     */
    public function test_jeune_can_update_own_profile(): void
    {
        $this->assertTrue($this->jeune->can('update', $this->jeune));
    }

    /**
     * Jeune ne peut pas modifier le profil d'un autre utilisateur.
     */
    public function test_jeune_cannot_update_others_profile(): void
    {
        $this->assertFalse($this->jeune->can('update', $this->chef));
    }

    /**
     * Admin bypass — l'admin peut tout faire sur UserPolicy.
     */
    public function test_admin_bypasses_all_user_policy_checks(): void
    {
        $this->assertTrue($this->admin->can('create', User::class));
        $this->assertTrue($this->admin->can('delete', $this->jeune));
        $this->assertTrue($this->admin->can('export', User::class));
    }

    // ================================================================
    // GROUP POLICY
    // ================================================================

    /**
     * Chef peut gérer son propre groupe (view, update).
     */
    public function test_chef_can_view_and_update_own_group(): void
    {
        $this->assertTrue($this->chef->can('view', $this->groupA));
        $this->assertTrue($this->chef->can('update', $this->groupA));
    }

    /**
     * Chef de groupe A ne peut PAS accéder au groupe B.
     */
    public function test_chef_cannot_view_or_update_other_group(): void
    {
        $this->assertFalse($this->chef->can('view', $this->groupB));
        $this->assertFalse($this->chef->can('update', $this->groupB));
    }

    /**
     * Bureau peut voir tous les groupes (group.view).
     */
    public function test_bureau_can_view_any_group(): void
    {
        $this->assertTrue($this->bureau->can('view', $this->groupA));
        $this->assertTrue($this->bureau->can('view', $this->groupB));
    }

    /**
     * Jeune ne peut pas créer ni supprimer un groupe.
     */
    public function test_jeune_cannot_create_or_delete_group(): void
    {
        $this->assertFalse($this->jeune->can('create', Group::class));
        $this->assertFalse($this->jeune->can('delete', $this->groupA));
    }

    /**
     * Admin bypass — l'admin peut tout faire sur GroupPolicy.
     */
    public function test_admin_bypasses_all_group_policy_checks(): void
    {
        $this->assertTrue($this->admin->can('create', Group::class));
        $this->assertTrue($this->admin->can('update', $this->groupB));
        $this->assertTrue($this->admin->can('delete', $this->groupA));
    }

    // ================================================================
    // ATTENDANCE POLICY
    // ================================================================

    /**
     * Chef peut valider manuellement la présence d'un membre de son groupe.
     */
    public function test_chef_can_validate_attendance_for_own_group_member(): void
    {
        $activity = \App\Models\Activity::create([
            'title' => 'Activity Test',
            'description' => 'Test',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => \App\Enums\ActivityStatus::DRAFT,
            'type' => \App\Enums\ActivityType::REUNION
        ]);

        // Créer une présence pour le jeune (membre du groupeA du chef)
        $attendance = Attendance::create([
            'user_id'     => $this->jeune->id,
            'activity_id' => $activity->id,
            'status'      => \App\Enums\AttendanceStatus::ABSENT->value,
            'scan_source' => 'manual',
        ]);

        $this->assertTrue($this->chef->can('validateManual', $attendance));
    }

    /**
     * Chef NE PEUT PAS valider la présence d'un membre d'un autre groupe.
     */
    public function test_chef_cannot_validate_attendance_for_other_group_member(): void
    {
        $activity = \App\Models\Activity::create([
            'title' => 'Activity Test 2',
            'description' => 'Test 2',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => \App\Enums\ActivityStatus::DRAFT,
            'type' => \App\Enums\ActivityType::REUNION
        ]);

        // Créer un membre du groupeB (bureau est leader, et le bureau est aussi un user)
        $outsideMember = User::create([
            'name' => 'Étranger', 'first_name' => 'User',
            'email' => 'outside@presentia.org', 'password' => bcrypt('Outside@1234!'),
            'status' => UserStatus::ACTIVE,
        ]);
        $this->groupB->members()->attach($outsideMember->id, ['joined_at' => now()]);

        $attendance = Attendance::create([
            'user_id'     => $outsideMember->id,
            'activity_id' => $activity->id,
            'status'      => \App\Enums\AttendanceStatus::ABSENT->value,
            'scan_source' => 'manual',
        ]);

        $this->assertFalse($this->chef->can('validateManual', $attendance));
    }

    /**
     * Admin bypass — l'admin peut valider n'importe quelle présence.
     */
    public function test_admin_bypasses_attendance_policy(): void
    {
        $activity = \App\Models\Activity::create([
            'title' => 'Activity Test 3',
            'description' => 'Test 3',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => \App\Enums\ActivityStatus::DRAFT,
            'type' => \App\Enums\ActivityType::REUNION
        ]);

        $attendance = Attendance::create([
            'user_id'     => $this->jeune->id,
            'activity_id' => $activity->id,
            'status'      => \App\Enums\AttendanceStatus::ABSENT->value,
            'scan_source' => 'manual',
        ]);

        $this->assertTrue($this->admin->can('validateManual', $attendance));
    }

    // ================================================================
    // NOTIFICATION POLICY
    // ================================================================

    /**
     * Chef peut notifier son propre groupe mais pas un autre groupe.
     */
    public function test_chef_can_notify_own_group_only(): void
    {
        $policy = new NotificationPolicy();

        // Chef peut notifier son propre groupe
        $this->assertTrue($policy->sendToGroup($this->chef, $this->groupA)->allowed());

        // Chef ne peut PAS notifier le groupe B (dont il n'est pas le leader)
        $this->assertFalse($policy->sendToGroup($this->chef, $this->groupB)->allowed());
    }

    /**
     * Jeune ne peut envoyer aucune notification.
     */
    public function test_jeune_cannot_send_any_notification(): void
    {
        $policy = new NotificationPolicy();
        $this->assertFalse($policy->sendToAll($this->jeune)->allowed());
        $this->assertFalse($policy->sendToRole($this->jeune)->allowed());
    }

    // ================================================================
    // REPORT POLICY
    // ================================================================

    /**
     * Chef peut exporter le rapport de son groupe mais pas le rapport global.
     */
    public function test_chef_can_export_own_group_report_only(): void
    {
        $policy = new ReportPolicy();

        // Chef peut exporter son groupe
        $this->assertTrue($policy->exportGroup($this->chef, $this->groupA)->allowed());

        // Chef ne peut PAS exporter le groupe B
        $this->assertFalse($policy->exportGroup($this->chef, $this->groupB)->allowed());

        // Chef ne peut PAS exporter le rapport global
        $this->assertFalse($policy->exportGlobal($this->chef)->allowed());
    }

    /**
     * Bureau peut voir les stats globales et exporter le rapport global.
     */
    public function test_bureau_can_access_global_reports(): void
    {
        $policy = new ReportPolicy();
        $this->assertTrue($policy->viewGlobalStats($this->bureau)->allowed());
        $this->assertTrue($policy->exportGlobal($this->bureau)->allowed());
    }

    /**
     * Admin bypass — l'admin peut tout exporter.
     */
    public function test_admin_bypasses_report_policy(): void
    {
        $policy = new ReportPolicy();

        $this->assertTrue($policy->exportGlobal($this->admin)->allowed());
        $this->assertTrue($policy->exportGroup($this->admin, $this->groupB)->allowed());
    }
}
