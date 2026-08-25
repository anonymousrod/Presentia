<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Jobs\SendPasswordResetWhatsApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Role::create(['name' => 'Administrateur']);
    }

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Identifiant (Email ou Téléphone)');
    }

    public function test_user_with_email_receives_reset_link_when_entering_email()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'phone' => '123456789'
        ]);

        $response = $this->post(route('password.email'), [
            'identifier' => 'user@example.com'
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseCount('password_reset_requests', 0);
    }

    public function test_user_with_both_email_and_phone_creates_instant_whatsapp_reset_when_entering_phone()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'phone' => '123456789'
        ]);

        $response = $this->post(route('password.email'), [
            'identifier' => '123456789'
        ]);

        $response->assertSessionHas('status', 'Un mot de passe temporaire vient de vous être envoyé par WhatsApp.');

        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'status' => 'DONE'
        ]);

        Queue::assertPushed(SendPasswordResetWhatsApp::class);
    }

    public function test_user_without_email_creates_instant_whatsapp_reset()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => null,
            'phone' => '123456789'
        ]);

        $response = $this->post(route('password.email'), [
            'identifier' => '123456789'
        ]);

        $response->assertSessionHas('status', 'Un mot de passe temporaire vient de vous être envoyé par WhatsApp.');

        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'status' => 'DONE'
        ]);

        Queue::assertPushed(SendPasswordResetWhatsApp::class);
    }

    public function test_rate_limiting_is_applied()
    {
        $identifier = 'test@example.com';

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('password.email'), ['identifier' => $identifier]);
        }

        $response = $this->post(route('password.email'), ['identifier' => $identifier]);

        $response->assertSessionHasErrors('identifier');
        $this->assertTrue(session('errors')->get('identifier')[0] == __('passwords.throttled'));
    }
}
