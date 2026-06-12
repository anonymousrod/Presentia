<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->enum('status', ['PENDING', 'ACTIVE', 'INACTIVE', 'SUSPENDED'])
                  ->default('PENDING')
                  ->index();
            $table->string('photo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->date('birth_date')->nullable();

            // Profil Complété
            $table->string('education_field')->nullable();
            $table->string('professional_status')->nullable();
            $table->string('current_profession')->nullable();
            $table->string('education_level')->nullable();
            $table->string('residence_municipality')->nullable();
            $table->string('residence_neighborhood')->nullable();

            $table->unsignedInteger('qr_version')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
