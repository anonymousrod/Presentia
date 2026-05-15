<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // Nullable : certaines actions (ex: tentative de login) n'ont pas d'user authentifié
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('action')->index(); // ex: 'created', 'updated', 'login', 'export'
            // Relation polymorphique : couvre User, Group, Activity, Registration, Attendance
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable(); // Jamais de password, token, credentials
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']); // Index morphTo
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
