<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\AuditLog;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolymorphicAuditingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un administrateur connecté pour les actions d'audit
        $this->admin = User::create([
            'name'       => 'Admin',
            'first_name' => 'System',
            'email'      => 'admin.audit@' . config('app.name') . '.org',
            'password'   => bcrypt('Secret123!'),
            'status'     => UserStatus::ACTIVE,
        ]);
    }

    /**
     * Test que la création d'un utilisateur logue l'audit sans mot de passe.
     */
    public function test_user_creation_is_audited_excluding_password(): void
    {
        $this->actingAs($this->admin);

        $user = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'jean.dupont.audit@' . config('app.name') . '.org',
            'password'   => 'MonMotDePasseSecret123',
            'status'     => UserStatus::PENDING,
        ]);

        // Vérifier l'audit log
        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($this->admin->id, $log->user_id);
        $this->assertNull($log->old_values);

        // Le mot de passe ne doit absolument PAS figurer dans new_values
        $this->assertArrayNotHasKey('password', $log->new_values);
        $this->assertArrayNotHasKey('remember_token', $log->new_values);

        // Les informations non sensibles doivent figurer dans new_values
        $this->assertEquals('Dupont', $log->new_values['name']);
        $this->assertEquals('jean.dupont.audit@' . config('app.name') . '.org', $log->new_values['email']);
    }

    /**
     * Test que la modification d'un utilisateur logue uniquement les valeurs modifiées (sales) et exclut le password.
     */
    public function test_user_update_is_audited_excluding_password(): void
    {
        $this->actingAs($this->admin);

        $user = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'jean.dupont.audit@' . config('app.name') . '.org',
            'password'   => 'MonMotDePasseSecret123',
            'status'     => UserStatus::PENDING,
        ]);

        // Vider la table d'audit pour se focaliser uniquement sur la modification
        AuditLog::truncate();

        // Modifier des données (y compris le mot de passe)
        $user->update([
            'first_name' => 'Jean-Pierre',
            'password'   => 'NouveauMotDePasse456',
        ]);

        // Vérifier l'audit log
        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($this->admin->id, $log->user_id);

        // Seul first_name a changé de manière non secrète. password ne doit pas y être.
        $this->assertArrayHasKey('first_name', $log->old_values);
        $this->assertArrayHasKey('first_name', $log->new_values);
        $this->assertEquals('Jean', $log->old_values['first_name']);
        $this->assertEquals('Jean-Pierre', $log->new_values['first_name']);

        // Le mot de passe secret est absolument exclu
        $this->assertArrayNotHasKey('password', $log->old_values);
        $this->assertArrayNotHasKey('password', $log->new_values);

        // Email n'a pas changé, donc ne doit PAS être présent dans old_values/new_values (dirty changes uniquement)
        $this->assertArrayNotHasKey('email', $log->old_values);
        $this->assertArrayNotHasKey('email', $log->new_values);
    }

    /**
     * Test que la suppression d'un utilisateur logue correctement l'action.
     */
    public function test_user_deletion_is_audited(): void
    {
        $this->actingAs($this->admin);

        $user = User::create([
            'name'       => 'A Supprimer',
            'first_name' => 'Jean',
            'email'      => 'supprimer@' . config('app.name') . '.org',
            'password'   => 'Secret123',
        ]);

        AuditLog::truncate();

        $user->delete();

        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('A Supprimer', $log->old_values['name']);
        $this->assertNull($log->new_values);
        $this->assertArrayNotHasKey('password', $log->old_values);
    }

    /**
     * Test que les autres modèles polymorphes comme Group sont correctement logués.
     */
    public function test_group_polymorphic_auditing(): void
    {
        $this->actingAs($this->admin);

        // 1. Création de Groupe
        $group = Group::create([
            'name'        => 'Groupe de Test',
            'description' => 'Un super groupe d\'entraide',
            'category'    => 'Entraide',
            'leader_id'   => $this->admin->id,
        ]);

        $logCreate = AuditLog::where('auditable_type', Group::class)
            ->where('auditable_id', $group->id)
            ->where('action', 'created')
            ->first();

        $this->assertNotNull($logCreate);
        $this->assertEquals('Groupe de Test', $logCreate->new_values['name']);
        $this->assertNull($logCreate->old_values);

        // 2. Modification de Groupe
        $group->update([
            'description' => 'Une description modifiée de groupe',
        ]);

        $logUpdate = AuditLog::where('auditable_type', Group::class)
            ->where('auditable_id', $group->id)
            ->where('action', 'updated')
            ->first();

        $this->assertNotNull($logUpdate);
        $this->assertEquals('Un super groupe d\'entraide', $logUpdate->old_values['description']);
        $this->assertEquals('Une description modifiée de groupe', $logUpdate->new_values['description']);
        $this->assertArrayNotHasKey('name', $logUpdate->old_values); // name n'a pas changé

        // 3. Suppression de Groupe
        $group->delete();

        $logDelete = AuditLog::where('auditable_type', Group::class)
            ->where('auditable_id', $group->id)
            ->where('action', 'deleted')
            ->first();

        $this->assertNotNull($logDelete);
        $this->assertEquals('Groupe de Test', $logDelete->old_values['name']);
        $this->assertNull($logDelete->new_values);
    }
}
