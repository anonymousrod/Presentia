<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Cible polymorphique : ALL (null), Group, Role, User individuel
            $table->string('target_type')->nullable(); // ex: 'App\Models\Group', 'App\Models\User'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('title');
            $table->text('content');
            $table->enum('channel', ['in_app', 'whatsapp', 'both']);
            $table->timestamp('scheduled_at')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
