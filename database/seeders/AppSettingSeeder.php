<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AppSetting::firstOrCreate(
            ['id' => 1],
            [
                'favicon' => 'Icone J-EBER.png',
                'logo_sm' => 'Icone J-EBER.png',
                'logo_dark' => 'Icone J-EBER.png',
                'logo_light' => 'Icone J-EBER.png',
                'pdf_logo_1' => 'assets/images/logo-dark.png', 
                'pdf_logo_2' => 'assets/images/logo-dark.png',
                'default_avatar' => 'assets/images/users/avatar-1.jpg',
                'default_cover' => 'assets/images/profile-bg.jpg',
                'sidebar_bg_1' => 'assets/images/sidebar/img-1.jpg',
                'sidebar_bg_2' => 'assets/images/sidebar/img-2.jpg',
                'sidebar_bg_3' => 'assets/images/sidebar/img-3.jpg',
                'sidebar_bg_4' => 'assets/images/sidebar/img-4.jpg',
            ]
        );
    }
}
