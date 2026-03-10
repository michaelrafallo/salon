<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'credit_balance',
        'ghl_contact_id',
    ];

    protected function casts(): array
    {
        return [
            'credit_balance' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @return HasMany<CustomerCreditLedger, $this>
     */
    public function creditLedgers(): HasMany
    {
        return $this->hasMany(CustomerCreditLedger::class, 'customer_id');
    }

    /**
     * @return HasManyThrough<Payment, Appointment, $this>
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Appointment::class);
    }
}
