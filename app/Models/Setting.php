<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const UPDATED_AT = 'updated_at';

    public const CREATED_AT = null;

    protected $fillable = [
        'option_key',
        'option_value',
    ];

    /**
     * All allowed option keys (whitelist for secure updates).
     *
     * @return array<int, string>
     */
    public static function allowedKeys(): array
    {
        return [
            'business_name',
            'business_phone',
            'business_email',
            'business_address',
            'receipt_print_enabled',
            'receipt_include_business_info',
            'payment_authorize_net_enabled',
            'payment_authorize_net_api_login_id',
            'payment_authorize_net_transaction_key',
            'payment_authorize_net_tokenization',
            'payment_authorize_net_environment',
            'payment_nmi_enabled',
            'payment_nmi_username',
            'payment_nmi_password',
            'payment_nmi_fraud_detection',
            'tax_rate',
            'tax_name',
            'tax_apply_to_all',
            'currency_code',
            'turn_tracker_order',
            'discounts_enabled',
        ];
    }

    /**
     * Get all settings as key => value (option_value as string).
     *
     * @return array<string, string>
     */
    public static function getAllAsKeyValue(): array
    {
        return static::query()
            ->whereIn('option_key', static::allowedKeys())
            ->pluck('option_value', 'option_key')
            ->all();
    }

    /**
     * Set a single option (create or update). Value stored as string.
     */
    public static function set(string $key, mixed $value): void
    {
        if (! in_array($key, static::allowedKeys(), true)) {
            return;
        }
        if ($key === 'turn_tracker_order') {
            $value = in_array($value, ['lowest', 'highest'], true) ? $value : 'lowest';
        } else {
            $value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        }
        static::query()->updateOrInsert(
            ['option_key' => $key],
            ['option_value' => $value, 'updated_at' => now()]
        );
    }
}
