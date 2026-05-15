<?php

// ============================================================
// EXTRAIT à intégrer dans app/Models/User.php
// Ces méthodes sont requises par le critère d'acceptation TECH-002
// ============================================================

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use SoftDeletes;
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'name',
        'first_name',
        'phone',
        'email',
        'password',
        'status',
        'photo',
        'birth_date',
        'qr_version',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'birth_date' => 'date',
            'status'     => UserStatus::class,
        ];
    }

    // ----------------------------------------------------------------
    // Méthodes helper requises par TECH-002
    // ----------------------------------------------------------------

    /**
     * L'utilisateur possède-t-il un email ?
     */
    public function hasEmail(): bool
    {
        return ! empty($this->email);
    }

    /**
     * L'utilisateur possède-t-il un numéro de téléphone ?
     */
    public function hasPhone(): bool
    {
        return ! empty($this->phone);
    }

    /**
     * Canal préféré pour la réinitialisation de mot de passe.
     * Email prioritaire si disponible, sinon WhatsApp.
     */
    public function preferredResetChannel(): string
    {
        return $this->hasEmail() ? 'email' : 'whatsapp';
    }

    // ----------------------------------------------------------------
    // Relations
    // ----------------------------------------------------------------

    /**
     * Les groupes dont l'utilisateur est membre.
     */
    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('joined_at', 'left_at')
                    ->withTimestamps();
    }

    /**
     * Les groupes dont l'utilisateur est le chef.
     */
    public function ledGroups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    /**
     * Les inscriptions de l'utilisateur aux activités.
     */
    public function registrations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Les présences enregistrées pour l'utilisateur.
     */
    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
