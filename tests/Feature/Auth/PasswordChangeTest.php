<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\AuditLog;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private User $pendingUser;
    private User $activeUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialiser le seeder des rôles/permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->pendingUser = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'pending@' . config('app.name') . '.org',
            'password'   => bcrypt('Temporary123!'),
            'status'     => UserStatus::PENDING,
        ]);

        $this->activeUser = User::create([
            'name'       => 'Martin',
            'first_name' => 'Paul',
            'email'      => 'active@' . config('app.name') . '.org',
            'password'   => bcrypt('Active123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
    }

    /**
     * Test qu'un utilisateur PENDING accédant au Dashboard soit redirigé vers /password/change.
     */
    public function test_pending_user_is_redirected_to_password_change_page(): void
    {
        $this->actingAs($this->pendingUser);

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('password.change.show'));
        $response->assertSessionHas('warning');
    }

    /**
     * Test qu'un utilisateur ACTIVE puisse accéder normalement au Dashboard sans redirection.
     */
    public function test_active_user_can_access_dashboard(): void
    {
        $this->actingAs($this->activeUser);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test que /logout et /password/change soient exclus du middleware (pas de redirection).
     */
    public function test_pending_user_can_access_logout_and_password_change_routes(): void
    {
        $this->actingAs($this->pendingUser);

        // 1. Accès au formulaire de changement de mot de passe
        $responseShow = $this->get(route('password.change.show'));
        $responseShow->assertStatus(200);

        // 2. Accès à la déconnexion
        $responseLogout = $this->post(route('logout'));
        $responseLogout->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test qu'un mot de passe trop faible échoue à la validation.
     */
    public function test_password_change_fails_with_weak_passwords(): void
    {
        $this->actingAs($this->pendingUser);

        // Liste de mots de passe ne respectant pas les règles
        $weakPasswords = [
            'weak', // Trop court
            'weakpassword', // Pas de majuscule, chiffre, caractère spécial
            'Weakpassword', // Pas de chiffre, pas de caractère spécial
            'Weakpassword123', // Pas de caractère spécial
            'weakpassword123!', // Pas de majuscule
        ];

        foreach ($weakPasswords as $weakPassword) {
            $response = $this->post(route('password.change.update'), [
                'password'              => $weakPassword,
                'password_confirmation' => $weakPassword,
            ]);

            $response->assertSessionHasErrors('password');
            // Le statut doit rester PENDING
            $this->assertEquals(UserStatus::PENDING, $this->pendingUser->fresh()->status);
        }
    }

    /**
     * Test qu'un changement de mot de passe fort réussit, active l'utilisateur et logue l'audit.
     */
    public function test_password_change_succeeds_and_updates_user_status_and_logs_audit(): void
    {
        $this->actingAs($this->pendingUser);

        // Vider la table d'audit pour ce test
        AuditLog::truncate();

        $response = $this->post(route('password.change.update'), [
            'password'              => 'SuperSecurePassword123!',
            'password_confirmation' => 'SuperSecurePassword123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Vérifier que le statut est passé à ACTIVE et le mot de passe a changé
        $freshUser = $this->pendingUser->fresh();
        $this->assertEquals(UserStatus::ACTIVE, $freshUser->status);
        $this->assertTrue(Hash::check('SuperSecurePassword123!', $freshUser->password));

        // Vérifier qu'un log d'audit a été créé par notre Trait Auditable !
        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $freshUser->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        // L'audit doit contenir le changement de statut PENDING -> ACTIVE
        $this->assertEquals(UserStatus::PENDING->value, $log->old_values['status']);
        $this->assertEquals(UserStatus::ACTIVE->value, $log->new_values['status']);

        // Le mot de passe ne doit absolument PAS figurer dans l'audit (sécurité de la blacklist)
        $this->assertArrayNotHasKey('password', $log->old_values);
        $this->assertArrayNotHasKey('password', $log->new_values);
    }
}
