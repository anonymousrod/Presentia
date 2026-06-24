<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Culte', 'color' => '#3b82f6'],
            ['name' => 'Réunion', 'color' => '#10b981'],
            ['name' => 'Formation', 'color' => '#f59e0b'],
            ['name' => 'Sortie', 'color' => '#ec4899'],
            ['name' => 'École de dimanche', 'color' => '#8b5cf6'],
            ['name' => 'Autre', 'color' => '#6b7280'],
        ];

        foreach ($types as $type) {
            \App\Models\ActivityType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
