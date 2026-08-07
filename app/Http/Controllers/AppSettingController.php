<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\OptimizesImages;

class AppSettingController extends Controller
{
    use OptimizesImages;
    public function edit()
    {
        $setting = AppSetting::firstOrCreate(['id' => 1]);
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = AppSetting::firstOrCreate(['id' => 1]);

        $fields = [
            'favicon',
            'logo_sm',
            'logo_dark',
            'logo_light',
            'pdf_logo_1',
            'pdf_logo_2',
            'default_avatar',
            'default_cover',
            'sidebar_bg_1',
            'sidebar_bg_2',
            'sidebar_bg_3',
            'sidebar_bg_4',
            'auth_bg',
            'hero_image',
            'about_image',
        ];

        $data = [];

        // Save text fields
        $textFields = [
            'hero_title',
            'hero_subtitle',
            'about_history',
            'about_mission',
            'about_vision',
            'about_objectives',
            'contact_phone',
            'facebook_link',
            'tiktok_link',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // S'il y avait déjà un fichier dans 'settings', on peut le supprimer
                // if ($setting->$field && str_starts_with($setting->$field, 'settings/')) {
                //     Storage::disk('public')->delete($setting->$field);
                // }

                $path = $this->optimizeAndStoreImage($request->file($field), 'settings');
                $data[$field] = $path;
            }
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
