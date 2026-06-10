<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Création des chefs de groupe et des jeunes...');

        // 1. Création des chefs de groupe
        $chef1 = User::firstOrCreate(
            ['email' => 'chef1@eber.org'],
            [
                'name' => 'Martin',
                'first_name' => 'Paul',
                'phone' => '+22990000001',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $chef1->syncRoles(['Jeune', 'Chef de groupe']);

        $chef2 = User::firstOrCreate(
            ['email' => 'chef2@eber.org'],
            [
                'name' => 'Bernard',
                'first_name' => 'Sarah',
                'phone' => '+22990000002',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $chef2->syncRoles(['Jeune', 'Chef de groupe']);

        $chef3 = User::firstOrCreate(
            ['email' => 'chef3@eber.org'],
            [
                'name' => 'Laine',
                'first_name' => 'Marc',
                'phone' => '+22990000003',
                'password' => Hash::make('Password@1234!'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $chef3->syncRoles(['Jeune', 'Chef de groupe']);

        // 2. Création des membres (Jeunes)
        $jeunesData = [
            ['email' => 'jeune1@eber.org', 'first_name' => 'Jean', 'name' => 'Dupont', 'phone' => '+22990000004'],
            ['email' => 'jeune2@eber.org', 'first_name' => 'Alice', 'name' => 'Smith', 'phone' => '+22990000005'],
            ['email' => 'jeune3@eber.org', 'first_name' => 'Bob', 'name' => 'Johnson', 'phone' => '+22990000006'],
            ['email' => 'jeune4@eber.org', 'first_name' => 'Charlie', 'name' => 'Brown', 'phone' => '+22990000007'],
            ['email' => 'jeune5@eber.org', 'first_name' => 'Emma', 'name' => 'Watson', 'phone' => '+22990000008'],
            ['email' => 'jeune6@eber.org', 'first_name' => 'Julie', 'name' => 'Dubois', 'phone' => '+22990000009'],
            ['email' => 'jeune7@eber.org', 'first_name' => 'David', 'name' => 'Miller', 'phone' => '+22990000010'],
        ];

        foreach ($jeunesData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'first_name' => $data['first_name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('Password@1234!'),
                    'status' => UserStatus::ACTIVE,
                ]
            );
            $user->syncRoles(['Jeune']);
        }

        $this->command->info('✅ Chefs de groupe et jeunes créés avec succès !');
    }
}
