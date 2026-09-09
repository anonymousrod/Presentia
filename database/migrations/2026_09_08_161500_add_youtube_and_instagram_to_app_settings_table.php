<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('youtube_link')->nullable()->after('tiktok_link');
            $table->string('instagram_link')->nullable()->after('youtube_link');
            $table->string('whatsapp_link')->nullable()->after('instagram_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['youtube_link', 'instagram_link', 'whatsapp_link']);
        });
    }
};
