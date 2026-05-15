<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['CULTE', 'REUNION', 'FORMATION', 'SORTIE', 'AUTRE']);
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'CANCELLED', 'ARCHIVED'])
                  ->default('DRAFT')
                  ->index();
            $table->enum('visibility', ['ALL', 'GROUP', 'ROLE'])
                  ->default('ALL');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->foreignId('responsible_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->unsignedInteger('qr_version')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};