<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;

class Gallery extends Model
{
    use HasFactory;
    use HasHashid;

    protected $fillable = [
        'title',
        'image_path',
        'type',
        'is_active',
    ];
}
