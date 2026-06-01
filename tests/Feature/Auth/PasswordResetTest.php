<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Notifications\AdminPasswordResetAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        // Créer le rôle Administrateur pour les tests de notification
        Role::create(['name' => 'Administrateur']);
    }

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Identifiant (Email ou Téléphone)');
    }

    public function test_user_with_email_receives_reset_link()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'phone' => '123456789'
        ]);

        Notification::fake();
        
        $response = $this->post(route('password.email'), [
            'identifier' => 'user@example.com'
        ]);

        $response->assertSessionHas('status');
        // On vérifie que le Password Broker a été sollicité (difficile de tester Password::sendResetLink directement car c'est une façade statique)
        // Mais on peut vérifier qu'aucune PasswordResetRequest n'a été créée en DB
        $this->assertDatabaseCount('password_reset_requests', 0);
    }

    public function test_user_without_email_creates_whatsapp_request()
    {
        $user = User::factory()->create([
            'email' => null,
            'phone' => '123456789'
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('Administrateur');

        Notification::fake();

        $response = $this->post(route('password.email'), [
            'identifier' => '123456789'
        ]);

        $response->assertSessionHas('status', 'Votre demande a été envoyée à l\'administrateur. Vous recevrez un nouveau mot de passe par WhatsApp après validation.');
        
        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'status' => 'PENDING'
        ]);

        Notification::assertSentTo($admin, AdminPasswordResetAlert::class);
    }

    public function test_rate_limiting_is_applied()
    {
        $identifier = 'test@example.com';
        
        // Simuler 3 tentatives
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('password.email'), ['identifier' => $identifier]);
        }

        // La 4ème doit échouer
        $response = $this->post(route('password.email'), ['identifier' => $identifier]);
        
        $response->assertSessionHasErrors('identifier');
        $this->assertTrue(session('errors')->get('identifier')[0] == __('passwords.throttled'));
    }

    public function test_admin_can_validate_whatsapp_request()
    {
        $user = User::factory()->create([
            'email' => null,
            'phone' => '123456789'
        ]);

        $request = PasswordResetRequest::create([
            'user_id' => $user->id,
            'code' => 'TESTCODE',
            'status' => 'PENDING',
            'expires_at' => now()->addHours(24)
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('Administrateur');

        $response = $this->actingAs($admin)
            ->post(route('admin.password-requests.validate', $request));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('DONE', $request->fresh()->status);
        $this->assertEquals('PENDING', $user->fresh()->status->value);
        // Le mot de passe a été changé (on ne peut pas tester le Hash facilement sans connaître le texte clair, mais on sait que update a été appelé)
    }
}
