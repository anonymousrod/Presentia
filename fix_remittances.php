<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Remittance::whereNull('group_id')->get() as $rem) {
    $contrib = $rem->contributions()->first();
    if ($contrib) {
        $user = $contrib->user;
        if ($user) {
            $group = $user->groups()->first();
            if ($group) {
                $rem->group_id = $group->id;
                $rem->save();
                echo 'Remittance ' . $rem->id . ' updated to group ' . $group->name . PHP_EOL;
            }
        }
    }
}
echo "Done.\n";
