<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::firstOrCreate(["name" => "Chargé de collecte"]);
$perm = Permission::firstOrCreate(["name" => "finance.collect_own_group"]);
$role->givePermissionTo($perm);
echo "Role created and permission assigned";
