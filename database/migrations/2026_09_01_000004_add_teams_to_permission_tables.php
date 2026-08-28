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
        $teamForeignKey = 'church_id';

        Schema::disableForeignKeyConstraints();

        // 1. Modifier la table 'roles'
        if (!Schema::hasColumn('roles', $teamForeignKey)) {
            Schema::table('roles', function (Blueprint $table) use ($teamForeignKey) {
                $table->unsignedBigInteger($teamForeignKey)->nullable()->after('id');
                $table->index($teamForeignKey, 'roles_church_id_index');
                $table->dropUnique('roles_name_guard_name_unique');
            });

            try {
                Schema::table('roles', function (Blueprint $table) {
                    $table->dropUnique('roles_code_unique');
                });
            } catch (\Exception $e) {
            }

            Schema::table('roles', function (Blueprint $table) use ($teamForeignKey) {
                $table->unique([$teamForeignKey, 'name', 'guard_name'], 'roles_church_id_name_guard_unique');
            });
        }

        // 2. Modifier la table 'model_has_roles'
        if (!Schema::hasColumn('model_has_roles', $teamForeignKey)) {
            Schema::table('model_has_roles', function (Blueprint $table) use ($teamForeignKey) {
                try {
                    $table->dropForeign(['role_id']);
                } catch (\Exception $e) {
                }

                $table->dropPrimary();
                $table->unsignedBigInteger($teamForeignKey)->default(0)->first();
                $table->index($teamForeignKey, 'model_has_roles_church_id_index');
                $table->primary([$teamForeignKey, 'role_id', 'model_id', 'model_type'], 'model_has_roles_church_role_model_primary');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }

        // 3. Modifier la table 'model_has_permissions'
        if (!Schema::hasColumn('model_has_permissions', $teamForeignKey)) {
            Schema::table('model_has_permissions', function (Blueprint $table) use ($teamForeignKey) {
                try {
                    $table->dropForeign(['permission_id']);
                } catch (\Exception $e) {
                }

                $table->dropPrimary();
                $table->unsignedBigInteger($teamForeignKey)->default(0)->first();
                $table->index($teamForeignKey, 'model_has_permissions_church_id_index');
                $table->primary([$teamForeignKey, 'permission_id', 'model_id', 'model_type'], 'model_has_permissions_church_permission_model_primary');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
