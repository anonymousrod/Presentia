<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\OptimizesImages;

class AppSettingController extends Controller
{
    use OptimizesImages;
    protected function getActiveChurchId(): int
    {
        return session('tenant_church_id') ?? auth()->user()?->church_id ?? 1;
    }

    public function edit()
    {
        $churchId = $this->getActiveChurchId();
        $church = \App\Models\Church::find($churchId);
        $setting = AppSetting::firstOrCreate(['church_id' => $churchId]);
        return view('admin.settings.edit', compact('setting', 'church'));
    }

    public function update(Request $request)
    {
        $churchId = $this->getActiveChurchId();
        $setting = AppSetting::firstOrCreate(['church_id' => $churchId]);
        
        // Seul le Super Admin HORS mode support peut modifier les logos globaux de la plateforme
        $isSuperAdmin = (auth()->user()?->isSuperAdmin() ?? false) && !session()->has('tenant_church_id');

        // Champs de fichiers autorisés selon le rôle
        $fileFields = [
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

        // Seul le Super Admin a le droit de modifier les logos globaux de la plateforme MeVoici
        if ($isSuperAdmin) {
            $fileFields = array_merge(['favicon', 'logo_sm', 'logo_dark', 'logo_light'], $fileFields);
        }

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

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $this->optimizeAndStoreImage($request->file($field), 'settings');
                $data[$field] = $path;

                // Si le Super Admin modifie les logos de la plateforme, on synchronise également le master setting (id: 1)
                if ($isSuperAdmin && in_array($field, ['favicon', 'logo_sm', 'logo_dark', 'logo_light'])) {
                    AppSetting::where('id', 1)->update([$field => $path]);
                }
            }
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}
