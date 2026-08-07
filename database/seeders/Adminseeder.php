<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@eber.org'],
            [
                'name'       => 'Administrateur',
                'first_name' => 'ÉBER',
                'email'      => 'admin@eber.org',
                'phone'      => '69129089',
                'password'   => Hash::make('Admin@1234!'), // À changer impérativement en production
                'status'     => 'ACTIVE',                  // L'admin est actif dès le départ
                'birth_date' => null,
            ]
        );

        // Attribuer le rôle Administrateur (créé par RolesAndPermissionsSeeder)
        if (! $admin->hasRole('Administrateur')) {
            $admin->assignRole('Administrateur');
        }

        $this->command->info('✅ Compte administrateur créé : admin@eber.org');
    }
}
