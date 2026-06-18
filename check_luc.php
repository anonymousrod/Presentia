<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('name', 'like', '%Agbessi%')->first();
if ($u) {
    echo "User: {$u->name} {$u->first_name}\n";
    echo "Roles: " . $u->roles->pluck('name')->join(', ') . "\n";
    echo "Permissions (direct): " . $u->getDirectPermissions()->pluck('name')->join(', ') . "\n";
    echo "Permissions (via roles): " . $u->getPermissionsViaRoles()->pluck('name')->join(', ') . "\n";
    echo "Led groups: " . $u->ledGroups()->pluck('name')->join(', ') . "\n";
} else {
    echo "User not found.\n";
}
