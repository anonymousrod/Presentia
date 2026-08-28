<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class ActivityType extends Model
{
    use HasHashid;
    use Auditable;
    use BelongsToChurch;
    protected $fillable = ['church_id', 'name', 'color'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
