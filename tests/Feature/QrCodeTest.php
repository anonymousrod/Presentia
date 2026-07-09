<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Activity;
use App\Enums\UserStatus;
use App\Enums\ActivityType;
use App\Enums\ActivityStatus;
use App\Enums\ActivityVisibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Activity $activity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Crée l'administrateur
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin@' . config('app.name') . '.org',
            'password'   => bcrypt('Admin@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        // Crée un membre classique
        $this->member = User::create([
            'name'       => 'Jeune',
            'first_name' => 'Simple',
            'email'      => 'jeune@' . config('app.name') . '.org',
            'password'   => bcrypt('Jeune@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->member->assignRole('Jeune');

        // Crée une activité test
        $this->activity = Activity::create([
            'title'       => 'Activité Test QR',
            'description' => 'Description test',
            'type'        => ActivityType::FORMATION,
            'status'      => ActivityStatus::PUBLISHED,
            'visibility'  => ActivityVisibility::ALL,
            'start_time'  => now()->addHour(),
            'end_time'    => now()->addHours(3),
            'capacity'    => 10,
            'qr_version'  => 1,
        ]);

        \App\Models\Registration::create([
            'user_id'       => $this->member->id,
            'activity_id'   => $this->activity->id,
            'status'        => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);
    }

    public function test_admin_can_generate_signed_url(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.activities.qr.generate', $this->activity));

        $response->assertRedirect(route('admin.activities.show', $this->activity));
        $response->assertSessionHas("activity_qr_url_{$this->activity->id}");
        $response->assertSessionHas("activity_qr_expires_{$this->activity->id}");

        $url = session("activity_qr_url_{$this->activity->id}");

        // Le scan de l'URL signée par le membre doit réussir
        $scanResponse = $this->actingAs($this->member)->followingRedirects()->get($url);
        $scanResponse->assertStatus(200);
        $scanResponse->assertSee('Présence Validée !');
        $scanResponse->assertSee($this->activity->title);
    }

    /**
     * Test qu'une URL expirée renvoie une erreur 403.
     */
    public function test_signed_url_expires_correctly(): void
    {
        // Génère une URL signée expirée il y a 5 minutes
        $expiredUrl = URL::temporarySignedRoute(
            'attendance.validate',
            now()->subMinutes(5),
            ['activity' => $this->activity->id, 'v' => $this->activity->qr_version]
        );

        $response = $this->actingAs($this->member)->get($expiredUrl);
        $response->assertStatus(403);
    }

    /**
     * Test qu'une URL signée modifiée (falsification) renvoie une erreur 403.
     */
    public function test_modified_url_returns_403(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.activities.qr.generate', $this->activity));

        $url = session("activity_qr_url_{$this->activity->id}");

        // Modifie la version du QR Code dans l'URL pour la rendre invalide
        $modifiedUrl = str_replace('v=' . $this->activity->qr_version, 'v=' . ($this->activity->qr_version + 1), $url);

        $scanResponse = $this->actingAs($this->member)->get($modifiedUrl);
        $scanResponse->assertStatus(403);
    }

    /**
     * Test que la révocation incrémente la version du QR Code et invalide les anciennes URLs.
     */
    public function test_revoke_increments_qr_version(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.activities.qr.generate', $this->activity));

        $oldUrl = session("activity_qr_url_{$this->activity->id}");

        // Révoquer le QR Code
        $revokeResponse = $this->actingAs($this->admin)
            ->post(route('admin.activities.qr.revoke', $this->activity));

        $revokeResponse->assertRedirect(route('admin.activities.show', $this->activity));
        $this->assertEquals(2, $this->activity->fresh()->qr_version);

        // Les variables de session doivent être effacées
        $this->assertNull(session("activity_qr_url_{$this->activity->id}"));
        $this->assertNull(session("activity_qr_expires_{$this->activity->id}"));

        // L'ancienne URL de scan doit être refusée avec un 403
        $scanResponse = $this->actingAs($this->member)->get($oldUrl);
        $scanResponse->assertStatus(403);
    }

    /**
     * Test que l'admin peut télécharger le PDF du QR Code de présence.
     */
    public function test_admin_can_download_pdf(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.activities.qr.generate', $this->activity));

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activities.qr.pdf', $this->activity));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('pdf', $response->headers->get('Content-Disposition'));
    }
}
