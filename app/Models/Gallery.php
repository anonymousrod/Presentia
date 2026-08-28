<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class Gallery extends Model
{
    use HasFactory;
    use HasHashid;
    use Auditable;
    use BelongsToChurch;

    protected $fillable = [
        'church_id',
        'title',
        'image_path',
        'type',
        'is_active',
    ];
}
