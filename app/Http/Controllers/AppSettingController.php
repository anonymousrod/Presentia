<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
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
        ];

        $data = [];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // S'il y avait déjà un fichier dans 'settings', on peut le supprimer
                // if ($setting->$field && str_starts_with($setting->$field, 'settings/')) {
                //     Storage::disk('public')->delete($setting->$field);
                // }

                $path = $request->file($field)->store('settings', 'public');
                $data[$field] = $path;
            }
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
