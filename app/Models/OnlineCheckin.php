<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineCheckin extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'appointment_id',
        'phone',
        'firstname',
        'lastname',
        'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
