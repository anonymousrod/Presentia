<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;
use App\Traits\Auditable;

class ActivityType extends Model
{
    use HasHashid;
    use Auditable;
    protected $fillable = ['name', 'color'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
