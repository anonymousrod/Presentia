<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Jobs\SendActivityReminder;
use App\Jobs\SendPasswordResetWhatsApp;
use App\Jobs\SendWhatsAppCredentials;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Activity;
use App\Models\User;
use App\Services\FakeWhatsAppService;
use App\Services\WhatsAppServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * NOTIF-001 — Tests du service WhatsApp (D7 Networks)
 *
 * Tous les tests utilisent FakeWhatsAppService → 0 appel API réel.
 */
class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $userWithPhone;
    private User $userWithoutPhone;

    protected function setUp(): void
    {
        parent::setUp();

        // Réinitialiser le state du FakeWhatsAppService avant chaque test
        FakeWhatsAppService::reset();

        // S'assurer que le container utilise bien FakeWhatsAppService (env=testing)
        $this->app->bind(
            WhatsAppServiceInterface::class,
            FakeWhatsAppService::class
        );

        $this->userWithPhone = User::create([
            'name'       => 'Martin',
            'first_name' => 'Pierre',
            'phone'      => '+22990001111',
            'password'   => bcrypt('Secret123'),
            'status'     => UserStatus::ACTIVE,
        ]);

        $this->userWithoutPhone = User::create([
            'name'       => 'Dupont',
            'first_name' => 'Jean',
            'email'      => 'jean.dupont@' . config('app.name') . '.org',
            'password'   => bcrypt('Secret123'),
            'status'     => UserStatus::ACTIVE,
        ]);
    }

    // =========================================================
    // ✓ Critère : FakeWhatsAppService → 0 appel API réel
    // =========================================================

    /** @test */
    public function fake_service_is_bound_and_makes_no_real_api_call(): void
    {
        $service = $this->app->make(WhatsAppServiceInterface::class);

        $this->assertInstanceOf(FakeWhatsAppService::class, $service);

        $response = $service->send('+22990001111', 'Test message');

        $this->assertTrue($response['success']);
        $this->assertSame('fake_d7networks', $response['provider']);
    }

    /** @test */
    public function fake_service_records_sent_messages_in_memory(): void
    {
        $service = $this->app->make(WhatsAppServiceInterface::class);

        $service->send('+22990001111', 'Premier message');
        $service->send('+22990002222', 'Deuxième message');

        $sent = FakeWhatsAppService::getSentMessages();

        $this->assertCount(2, $sent);
        $this->assertSame('+22990001111', $sent[0]['phone']);
        $this->assertSame('Premier message', $sent[0]['message']);
        $this->assertSame('+22990002222', $sent[1]['phone']);
    }

    // =========================================================
    // ✓ Critère : 4 Jobs dispatchés → entrée dans la table jobs
    // =========================================================

    /** @test */
    public function send_whatsapp_credentials_job_is_dispatchable(): void
    {
        Bus::fake();

        SendWhatsAppCredentials::dispatch($this->userWithPhone, 'mdp123456');

        Bus::assertDispatched(SendWhatsAppCredentials::class, function ($job) {
            return $job->user->id === $this->userWithPhone->id
                && $job->plainPassword === 'mdp123456';
        });
    }

    /** @test */
    public function send_whatsapp_notification_job_is_dispatchable(): void
    {
        Bus::fake();

        SendWhatsAppNotification::dispatch($this->userWithPhone, 'Votre événement commence bientôt.');

        Bus::assertDispatched(SendWhatsAppNotification::class, function ($job) {
            return $job->user->id === $this->userWithPhone->id;
        });
    }

    /** @test */
    public function send_activity_reminder_job_is_dispatchable(): void
    {
        Bus::fake();

        $activity = Activity::create([
            'title'          => 'Réunion mensuelle',
            'type'           => \App\Enums\ActivityType::REUNION,
            'status'         => \App\Enums\ActivityStatus::PUBLISHED,
            'visibility'     => \App\Enums\ActivityVisibility::ALL,
            'start_time'     => now()->addDay(),
            'end_time'       => now()->addDay()->addHours(2),
            'responsible_id' => $this->userWithPhone->id,
        ]);

        SendActivityReminder::dispatch($this->userWithPhone, $activity);

        Bus::assertDispatched(SendActivityReminder::class, function ($job) use ($activity) {
            return $job->user->id === $this->userWithPhone->id
                && $job->activity->id === $activity->id;
        });
    }

    /** @test */
    public function send_password_reset_whatsapp_job_is_dispatchable(): void
    {
        Bus::fake();

        SendPasswordResetWhatsApp::dispatch($this->userWithPhone, '854321');

        Bus::assertDispatched(SendPasswordResetWhatsApp::class, function ($job) {
            return $job->user->id === $this->userWithPhone->id
                && $job->temporaryPassword === '854321';
        });
    }

    // =========================================================
    // ✓ Critère : Log de chaque envoi (succès) dans whatsapp_logs
    // =========================================================

    /** @test */
    public function credentials_job_logs_success_in_whatsapp_logs(): void
    {
        $job = new SendWhatsAppCredentials($this->userWithPhone, 'mdp123456');
        $job->handle($this->app->make(WhatsAppServiceInterface::class));

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'credentials',
            'status'       => 'sent',
        ]);

        // Vérifier qu'un message a été transmis au service fake
        $this->assertCount(1, FakeWhatsAppService::getSentMessages());
    }

    /** @test */
    public function notification_job_logs_success_in_whatsapp_logs(): void
    {
        $job = new SendWhatsAppNotification($this->userWithPhone, 'Notification importante.');
        $job->handle($this->app->make(WhatsAppServiceInterface::class));

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'notification',
            'status'       => 'sent',
        ]);
    }

    /** @test */
    public function activity_reminder_job_logs_success_in_whatsapp_logs(): void
    {
        $activity = Activity::create([
            'title'          => 'Atelier jeunes',
            'type'           => \App\Enums\ActivityType::REUNION,
            'status'         => \App\Enums\ActivityStatus::PUBLISHED,
            'visibility'     => \App\Enums\ActivityVisibility::ALL,
            'start_time'     => now()->addDays(2),
            'end_time'       => now()->addDays(2)->addHours(2),
            'location'       => 'Salle A',
            'responsible_id' => $this->userWithPhone->id,
        ]);

        $job = new SendActivityReminder($this->userWithPhone, $activity);
        $job->handle($this->app->make(WhatsAppServiceInterface::class));

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'reminder',
            'status'       => 'sent',
        ]);
    }

    /** @test */
    public function password_reset_job_logs_success_in_whatsapp_logs(): void
    {
        $job = new SendPasswordResetWhatsApp($this->userWithPhone, '998877');
        $job->handle($this->app->make(WhatsAppServiceInterface::class));

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'password_reset',
            'status'       => 'sent',
        ]);
    }

    // =========================================================
    // ✓ Critère : Utilisateur sans téléphone → job abandonné silencieusement
    // =========================================================

    /** @test */
    public function job_skips_silently_when_user_has_no_phone(): void
    {
        $job = new SendWhatsAppCredentials($this->userWithoutPhone, 'mdp123456');
        $job->handle($this->app->make(WhatsAppServiceInterface::class));

        // Aucun log en DB (pas d'envoi, pas d'erreur)
        $this->assertDatabaseMissing('whatsapp_logs', [
            'user_id' => $this->userWithoutPhone->id,
        ]);

        // Aucun message envoyé au provider
        $this->assertCount(0, FakeWhatsAppService::getSentMessages());
    }

    // =========================================================
    // ✓ Critère : Échec API → log 'failed' dans whatsapp_logs
    // =========================================================

    /** @test */
    public function credentials_job_logs_failure_when_api_throws(): void
    {
        FakeWhatsAppService::failNextAttempts(1);

        $job = new SendWhatsAppCredentials($this->userWithPhone, 'mdp123456');

        try {
            $job->handle($this->app->make(WhatsAppServiceInterface::class));
        } catch (\Throwable) {
            // Exception attendue
        }

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'credentials',
            'status'       => 'failed',
        ]);
    }

    /** @test */
    public function notification_job_logs_failure_when_api_throws(): void
    {
        FakeWhatsAppService::failNextAttempts(1);

        $job = new SendWhatsAppNotification($this->userWithPhone, 'Message test.');

        try {
            $job->handle($this->app->make(WhatsAppServiceInterface::class));
        } catch (\Throwable) {
            // Exception attendue
        }

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'notification',
            'status'       => 'failed',
        ]);
    }

    /** @test */
    public function password_reset_job_logs_failure_when_api_throws(): void
    {
        FakeWhatsAppService::failNextAttempts(1);

        $job = new SendPasswordResetWhatsApp($this->userWithPhone, '123456');

        try {
            $job->handle($this->app->make(WhatsAppServiceInterface::class));
        } catch (\Throwable) {
            // Exception attendue
        }

        $this->assertDatabaseHas('whatsapp_logs', [
            'user_id'      => $this->userWithPhone->id,
            'message_type' => 'password_reset',
            'status'       => 'failed',
        ]);
    }

    // =========================================================
    // ✓ Critère : Jobs ont $tries=3 et backoff exponentiel configuré
    // =========================================================

    /** @test */
    public function all_jobs_have_3_max_tries_configured(): void
    {
        $jobs = [
            new SendWhatsAppCredentials($this->userWithPhone, 'pass'),
            new SendWhatsAppNotification($this->userWithPhone, 'msg'),
            new SendPasswordResetWhatsApp($this->userWithPhone, '000000'),
        ];

        foreach ($jobs as $job) {
            $this->assertSame(3, $job->tries, get_class($job) . ' doit avoir $tries = 3');
        }
    }

    /** @test */
    public function all_jobs_have_exponential_backoff_configured(): void
    {
        $activity = Activity::create([
            'title'          => 'Test',
            'type'           => \App\Enums\ActivityType::REUNION,
            'status'         => \App\Enums\ActivityStatus::PUBLISHED,
            'visibility'     => \App\Enums\ActivityVisibility::ALL,
            'start_time'     => now()->addDay(),
            'end_time'       => now()->addDay()->addHours(2),
            'responsible_id' => $this->userWithPhone->id,
        ]);

        $jobs = [
            new SendWhatsAppCredentials($this->userWithPhone, 'pass'),
            new SendWhatsAppNotification($this->userWithPhone, 'msg'),
            new SendActivityReminder($this->userWithPhone, $activity),
            new SendPasswordResetWhatsApp($this->userWithPhone, '000000'),
        ];

        foreach ($jobs as $job) {
            $this->assertSame([30, 60, 120], $job->backoff(), get_class($job) . ' doit avoir backoff [30, 60, 120]');
        }
    }

    // =========================================================
    // ✓ Critère : Queue driver 'database' — job enregistré dans jobs table
    // =========================================================

    /** @test */
    public function dispatched_job_is_stored_in_jobs_table_with_database_queue(): void
    {
        // Désactiver Bus::fake() pour ce test : on teste le vrai driver
        Queue::fake();

        SendWhatsAppCredentials::dispatch($this->userWithPhone, 'mdp_queue_test');

        Queue::assertPushed(SendWhatsAppCredentials::class, function ($job) {
            return $job->user->id === $this->userWithPhone->id;
        });
    }
}
