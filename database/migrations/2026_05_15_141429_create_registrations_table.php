<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('activity_id')
                  ->constrained('activities')
                  ->cascadeOnDelete();
            $table->enum('status', ['PRESENT', 'ABSENT_JUSTIFIED', 'UNCERTAIN'])
                  ->default('PRESENT');
            $table->text('justification')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->boolean('is_waitlisted')->default(false);
            $table->timestamps();

            // Un jeune ne peut s'inscrire qu'une seule fois par activité
            $table->unique(['user_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
