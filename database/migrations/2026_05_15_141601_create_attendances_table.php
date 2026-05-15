<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('activity_id')
                  ->constrained('activities')
                  ->cascadeOnDelete();
            $table->enum('status', ['PRESENT', 'LATE', 'ABSENT', 'EXCUSED']);
            // Distingue scan QR automatique vs validation manuelle du chef
            $table->enum('scan_source', ['qr_code', 'manual']);
            $table->text('note')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
            $table->string('ip_address', 45)->nullable(); // 45 chars pour IPv6
            $table->timestamps();

            // Idempotence : un seul enregistrement de présence par jeune par activité
            $table->unique(['user_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};