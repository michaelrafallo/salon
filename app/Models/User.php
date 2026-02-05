<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'role',
        'status',
        'initials',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsToMany<Appointment, $this>
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_technician')
            ->withPivot('tip')
            ->withTimestamps();
    }

    /**
     * @return HasOne<TurnTracker, $this>
     */
    public function turnTracker(): HasOne
    {
        return $this->hasOne(TurnTracker::class);
    }

    /**
     * Scope: salon staff (admin, receptionist, technician).
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeSalonStaff(Builder $query): Builder
    {
        return $query->whereIn('role', ['admin', 'receptionist', 'technician']);
    }

    /**
     * Scope: active technicians only.
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeTechnicians(Builder $query): Builder
    {
        return $query->where('role', 'technician')
            ->where(function (Builder $q) {
                $q->where('status', 'active')->orWhereNull('status');
            });
    }
}
