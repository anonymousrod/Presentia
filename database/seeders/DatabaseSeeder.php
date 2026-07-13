<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            ActivityTypeSeeder::class,
            ChartDataSeeder::class, //pas en production
            // MemberSeeder::class,
            // ExecutiveSeeder::class,
            // GroupSeeder::class,
            // ActivitySeeder::class,
        ]);
    }
}
