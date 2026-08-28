<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AppSetting extends Model
{
    use HasFactory;
    use Auditable;

    public function church(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    protected $fillable = [
        'church_id',
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
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'about_history',
        'about_mission',
        'about_vision',
        'about_objectives',
        'about_image',
        'contact_phone',
        'facebook_link',
        'tiktok_link',
    ];

    public function getFaviconUrlAttribute()
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : asset('assets/images/favicon.ico');
    }

    public function getLogoSmUrlAttribute()
    {
        return $this->logo_sm ? asset('storage/' . $this->logo_sm) : asset('assets/images/logo-sm.png');
    }

    public function getLogoDarkUrlAttribute()
    {
        return $this->logo_dark ? asset('storage/' . $this->logo_dark) : asset('assets/images/logo-dark.png');
    }

    public function getLogoLightUrlAttribute()
    {
        return $this->logo_light ? asset('storage/' . $this->logo_light) : asset('assets/images/logo-light.png');
    }
}
