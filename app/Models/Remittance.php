<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashid;
use App\Traits\Auditable;
use App\Traits\BelongsToChurch;

class Remittance extends Model
{
    use HasFactory;
    use HasHashid;
    use Auditable;
    use BelongsToChurch;

    protected $fillable = [
        'church_id',
        'group_id',
        'collector_id',
        'treasurer_id',
        'amount',
        'status',
        'validated_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function treasurer()
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }

    public function contributions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Le groupe concerné par ce versement.
     */
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
