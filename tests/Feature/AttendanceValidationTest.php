<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AttendanceValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    }

    public function test_user_can_validate_attendance_with_valid_signed_url()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create([
            'start_time' => now()->addMinutes(10), // Pas encore commencé
            'qr_version' => 1
        ]);

        \App\Models\Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.status', AttendanceStatus::PRESENT->value);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => AttendanceStatus::PRESENT->value,
            'scan_source' => 'qr_code'
        ]);
    }

    public function test_user_is_marked_late_after_15_minutes()
    {
        $user = User::factory()->create();
        // Activité commencée il y a 20 minutes
        $activity = Activity::factory()->create([
            'start_time' => now()->subMinutes(20),
            'qr_version' => 1
        ]);

        \App\Models\Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', AttendanceStatus::LATE->value);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => AttendanceStatus::LATE->value
        ]);
    }

    public function test_cannot_validate_with_invalid_signature()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['qr_version' => 1]);

        \App\Models\Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        $url = route('attendance.validate', ['activity' => $activity->id, 'v' => $activity->qr_version]);
        // URL non signée

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403); // Middleware 'signed' rejette
    }

    public function test_cannot_validate_with_wrong_qr_version()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['qr_version' => 2]);

        \App\Models\Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        // On génère une URL signée pour la version 1 alors que l'activité est en version 2
        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => 1]
        );

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);
        $response->assertJsonPath('message', 'Ce QR Code a été révoqué ou mis à jour.');
    }

    public function test_attendance_is_idempotent_double_scan()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['qr_version' => 1]);

        \App\Models\Registration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'status' => 'PRESENT',
            'is_waitlisted' => false,
            'registered_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        // Premier scan
        $this->actingAs($user)->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);
        
        // Deuxième scan
        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonPath('already_scanned', true);
        
        $this->assertEquals(1, Attendance::where('user_id', $user->id)->where('activity_id', $activity->id)->count());
    }

    public function test_unregistered_user_cannot_validate_attendance_before_start()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create([
            'start_time' => now()->addMinutes(10), // Pas encore commencé
            'qr_version' => 1
        ]);

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('message', 'Vous ne pouvez pas valider votre présence sans être inscrit à cette activité.');
    }

    public function test_unregistered_user_cannot_validate_attendance_after_start_shows_extended_message()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create([
            'start_time' => now()->subMinutes(10), // Déjà commencé
            'qr_version' => 1
        ]);

        $url = URL::temporarySignedRoute(
            'attendance.validate',
            now()->addHour(),
            ['activity' => $activity->id, 'v' => $activity->qr_version]
        );

        $response = $this->actingAs($user)
            ->post($url, [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(403);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('message', "Vous ne pouvez pas valider votre présence sans être inscrit à cette activité. Vous ne pouvez plus vous inscrire à cette activité. Veuillez contacter votre responsable de groupe ou le Président de la jeunesse afin qu'il puisse marquer votre présence.");
    }
}
