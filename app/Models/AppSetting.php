<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];
}
