<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'appointment_id',
        'amount',
        'currency',
        'sub_total',
        'discount',
        'credits',
        'gift_card',
        'tax',
        'tip',
        'method',
        'status',
        'refund_notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sub_total' => 'decimal:2',
            'discount' => 'decimal:2',
            'credits' => 'decimal:2',
            'gift_card' => 'decimal:2',
            'tax' => 'decimal:2',
            'tip' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Appointment, $this>
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
