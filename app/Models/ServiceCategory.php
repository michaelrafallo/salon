<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'slug',
        'name',
    ];

    /**
     * @return BelongsToMany<Service, $this>
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_service_category')
            ->withTimestamps();
    }

    /**
     * @return HasMany<AppointmentService, $this>
     */
    public function appointmentServices(): HasMany
    {
        return $this->hasMany(AppointmentService::class, 'service_category_id');
    }
}
