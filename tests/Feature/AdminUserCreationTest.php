<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserStatus;
use App\Jobs\SendEmailCredentials;
use App\Jobs\SendWhatsAppCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AdminUserCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // 1. Initialiser le seeder des rôles/permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // 2. Créer un administrateur
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin@presentia.org',
            'password'   => bcrypt('Admin@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->admin->assignRole('Administrateur');

        // 3. Créer un membre régulier sans permission d'administration
        $this->member = User::create([
            'name'       => 'Jeune',
            'first_name' => 'Simple',
            'email'      => 'jeune@presentia.org',
            'password'   => bcrypt('Jeune@1234!'),
            'status'     => UserStatus::ACTIVE,
        ]);
        $this->member->assignRole('Jeune');
    }

    /**
     * Test que les non-admins ne peuvent pas accéder à la création de compte.
     */
    public function test_non_admin_cannot_access_user_creation(): void
    {
        // Invité non authentifié
        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('login'));

        // Membre authentifié mais sans rôle admin ou permission member.create
        $response = $this->actingAs($this->member)->get(route('admin.users.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)->post(route('admin.users.store'), [
            'name'       => 'Test',
            'first_name' => 'User',
            'email'      => 'test@presentia.org',
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test que l'admin peut accéder au formulaire.
     */
    public function test_admin_can_access_user_creation_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $response->assertStatus(200);
        $response->assertSee('Création de Compte');
    }

    /**
     * Test la validation : la création sans email ni téléphone échoue.
     */
    public function test_creation_fails_without_email_and_phone(): void
    {
        $response = $this->actingAs($this->admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name'       => 'Dupont',
                'first_name' => 'Jean',
                'birth_date' => '2000-01-01',
            ]);

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors(['email', 'phone']);
    }

    /**
     * Test la création avec Email seul : dispatche Job email.
     */
    public function test_creation_with_email_only_dispatches_email_job(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'jean.dupont@presentia.org',
            'birth_date' => '2000-01-01',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        // Vérifier l'utilisateur en DB
        $user = User::where('email', 'jean.dupont@presentia.org')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserStatus::PENDING, $user->status);
        $this->assertEquals('Dupont', $user->name);
        $this->assertEquals('Jean', $user->first_name);

        // Vérifier le Job d'email dispatché
        $plainPassword = null;
        Bus::assertDispatched(SendEmailCredentials::class, function ($job) use (&$plainPassword, $user) {
            if ($job->user->id === $user->id) {
                $plainPassword = $job->plainPassword;
                return strlen($plainPassword) === 10;
            }
            return false;
        });
        Bus::assertNotDispatched(SendWhatsAppCredentials::class);

        // Vérifier que le mot de passe est hashé en base
        $this->assertNotNull($plainPassword);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($plainPassword, $user->password));

        // Vérifier le log d'audit
        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'created',
            'auditable_type' => User::class,
            'auditable_id'   => $user->id,
            'user_id'        => $this->admin->id,
        ]);
    }

    /**
     * Test la création avec Téléphone seul : dispatche Job WhatsApp.
     */
    public function test_creation_with_phone_only_dispatches_whatsapp_job(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'       => 'Martin',
            'first_name' => 'Pierre',
            'phone'      => '+22990000000',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        // Vérifier l'utilisateur en DB
        $user = User::where('phone', '+22990000000')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserStatus::PENDING, $user->status);

        // Vérifier le Job WhatsApp dispatché
        Bus::assertDispatched(SendWhatsAppCredentials::class, function ($job) use ($user) {
            return $job->user->id === $user->id && strlen($job->plainPassword) === 10;
        });
        Bus::assertNotDispatched(SendEmailCredentials::class);
    }

    /**
     * Test la création avec les deux (Email + Téléphone) : Email prioritaire.
     */
    public function test_creation_with_both_prioritizes_email_job(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'       => 'Durand',
            'first_name' => 'Sophie',
            'email'      => 'sophie.durand@presentia.org',
            'phone'      => '+22991111111',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        $user = User::where('email', 'sophie.durand@presentia.org')->first();
        $this->assertNotNull($user);

        // Email est prioritaire
        Bus::assertDispatched(SendEmailCredentials::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
        Bus::assertNotDispatched(SendWhatsAppCredentials::class);
    }

    /**
     * Test que les doublons d'email existants échouent.
     */
    public function test_creation_fails_with_existing_email(): void
    {
        // Création initiale
        User::create([
            'name'       => 'Existant',
            'first_name' => 'User',
            'email'      => 'existant@presentia.org',
            'password'   => bcrypt('Secret123'),
        ]);

        // Tentative de doublon
        $response = $this->actingAs($this->admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name'       => 'Nouveau',
                'first_name' => 'User',
                'email'      => 'existant@presentia.org',
            ]);

        $response->assertRedirect(route('admin.users.create'));
        $response->assertSessionHasErrors(['email']);
    }
}
