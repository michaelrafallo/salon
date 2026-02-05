<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'description',
        'pin',
        'initial_value',
        'balance',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'initial_value' => 'decimal:2',
            'balance' => 'decimal:2',
            'active' => 'boolean',
        ];
    }
}
