<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$array = Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
    public function array(array $array) {}
}, 'Document/image_doc/Suivi des contributions - J-EBER 2026.xlsx');

print_r(array_slice($array[0], 0, 15));
