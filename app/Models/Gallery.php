<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;
use App\Traits\Auditable;

class Gallery extends Model
{
    use HasFactory;
    use HasHashid;
    use Auditable;

    protected $fillable = [
        'title',
        'image_path',
        'type',
        'is_active',
    ];
}
