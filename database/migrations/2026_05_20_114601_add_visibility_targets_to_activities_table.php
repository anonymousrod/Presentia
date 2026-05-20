<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('visibility_group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('visibility_role_id')->nullable()->constrained('roles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['visibility_group_id']);
            $table->dropForeign(['visibility_role_id']);
            $table->dropColumn(['visibility_group_id', 'visibility_role_id']);
        });
    }
};
