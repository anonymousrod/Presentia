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
        $tenantTables = [
            'users',
            'groups',
            'activities',
            'activity_types',
            'registrations',
            'attendances',
            'contributions',
            'remittances',
            'app_settings',
            'galleries',
            'audit_logs',
            'scheduled_notifications',
            'whatsapp_logs',
        ];

        foreach ($tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'church_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('church_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('churches')
                        ->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tenantTables = [
            'whatsapp_logs',
            'scheduled_notifications',
            'audit_logs',
            'galleries',
            'app_settings',
            'remittances',
            'contributions',
            'attendances',
            'registrations',
            'activity_types',
            'activities',
            'groups',
            'users',
        ];

        foreach ($tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'church_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['church_id']);
                    $table->dropColumn('church_id');
                });
            }
        }
    }
};
