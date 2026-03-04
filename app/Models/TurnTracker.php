<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TurnTracker extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'services',
        'clock_in',
        'clock_out',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'decimal:2',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
