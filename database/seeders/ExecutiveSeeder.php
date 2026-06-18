<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExecutiveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Création du bureau exécutif (Président, VP, Membres)...');

        $president = User::firstOrCreate(
            ['email' => 'president@eber.org'],
            [
                'name' => 'Koffi',
                'first_name' => 'Emmanuel',
                'phone' => '+22990000010',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $president->syncRoles(['Président']);

        $vp = User::firstOrCreate(
            ['email' => 'vp@eber.org'],
            [
                'name' => 'Dossou',
                'first_name' => 'Claire',
                'phone' => '+22990000011',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $vp->syncRoles(['Vice-président']);

        $bureau = User::firstOrCreate(
            ['email' => 'bureau@eber.org'],
            [
                'name' => 'Agbessi',
                'first_name' => 'Luc',
                'phone' => '+22990000012',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $bureau->syncRoles(['Membre du bureau']);

        $this->command->info('✅ Bureau exécutif créé avec succès !');
    }
}
