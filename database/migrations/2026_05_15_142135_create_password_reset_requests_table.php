<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cette table gère UNIQUEMENT le flux WhatsApp (utilisateurs sans email).
        // Le reset par email utilise le Password Broker natif Laravel (table password_reset_tokens).
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('code'); // Code envoyé par WhatsApp
            $table->enum('status', ['PENDING', 'DONE', 'EXPIRED'])
                  ->default('PENDING')
                  ->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};