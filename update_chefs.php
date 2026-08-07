<?php

$faker = \Faker\Factory::create('fr_FR');
$users = App\Models\User::where('first_name', 'Chef')->get();

foreach ($users as $u) {
    $u->first_name = $faker->firstName;
    $u->save();
    echo "Mis à jour: {$u->email} -> Nouveau prénom: {$u->first_name}\n";
}
