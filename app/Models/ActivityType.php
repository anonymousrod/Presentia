<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    protected $fillable = ['name', 'color'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
