<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class Contribution extends Model
{
    use HasFactory;
    use Auditable;
    use BelongsToChurch;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function remittance()
    {
        return $this->belongsTo(Remittance::class);
    }
}
