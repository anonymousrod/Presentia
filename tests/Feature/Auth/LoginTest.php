<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $password = 'Password123!';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'jean.dupont@presentia.org',
            'phone'      => '+22990000000',
            'password'   => bcrypt($this->password),
            'status'     => UserStatus::ACTIVE,
        ]);
    }

    /**
     * Test de l'affichage du formulaire de connexion.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('identifiant');
        $response->assertSee('password');
    }

    /**
     * Connexion réussie avec l'adresse email.
     */
    public function test_user_can_login_with_valid_email(): void
    {
        $response = $this->post(route('login'), [
            'identifiant' => $this->user->email,
            'password'    => $this->password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * Connexion réussie avec le numéro de téléphone.
     */
    public function test_user_can_login_with_valid_phone(): void
    {
        $response = $this->post(route('login'), [
            'identifiant' => $this->user->phone,
            'password'    => $this->password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * Échec de connexion avec des identifiants invalides (doit renvoyer un message générique).
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post(route('login'), [
            'identifiant' => 'inconnu@presentia.org',
            'password'    => 'MauvaisPassword',
        ]);

        $response->assertSessionHasErrors('identifiant');
        $this->assertGuest();

        // Le message d'erreur doit être générique (ex: auth.failed)
        $errors = session('errors')->get('identifiant');
        $this->assertEquals(__('auth.failed'), $errors[0]);
    }

    /**
     * Test du rate limiting : après 5 tentatives infructueuses, la 6ème est bloquée.
     */
    public function test_login_rate_limiting_locks_out_after_five_failed_attempts(): void
    {
        RateLimiter::clear(Str_lower($this->user->email) . '|127.0.0.1');

        // Faire 5 tentatives infructueuses
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('login'), [
                'identifiant' => $this->user->email,
                'password'    => 'MauvaisPassword',
            ], ['REMOTE_ADDR' => '127.0.0.1']);

            $response->assertSessionHasErrors('identifiant');
        }

        // La 6ème tentative doit échouer à cause du rate limiter (429 ou erreur de throttle)
        $response = $this->post(route('login'), [
            'identifiant' => $this->user->email,
            'password'    => $this->password, // Mot de passe correct cette fois-ci !
        ], ['REMOTE_ADDR' => '127.0.0.1']);

        $response->assertSessionHasErrors('identifiant');
        $this->assertGuest();

        $errors = session('errors')->get('identifiant');
        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }
}

// Fonction helper pour s'assurer que la clé de limitation est correctement formatée
function Str_lower(string $value): string
{
    return \Illuminate\Support\Str::lower($value);
}
