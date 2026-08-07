<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;

class ActivityType extends Model
{
    use HasHashid;
    protected $fillable = ['name', 'color'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
