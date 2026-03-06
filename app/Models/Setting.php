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

    protected static ?string $cachedCurrencyCode = null;

    protected static ?string $cachedCurrencySymbol = null;

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
            'tax_rate',
            'tax_name',
            'tax_apply_to_all',
            'currency_code',
            'commission_rate',
            'points_rate_fixed',
            'points_rate_percentage',
            'points_unit_value',
            'points_per_unit',
            'reward_percentage',
            'turn_tracker_order',
            'discounts_enabled',
            'gift_cards_enabled',
            'timezone',
            'ghl_webhook_book_appointment',
            'ghl_webhook_no_show_sms',
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

    public static function currencyCode(): string
    {
        if (static::$cachedCurrencyCode !== null) {
            return static::$cachedCurrencyCode;
        }

        $code = static::query()
            ->where('option_key', 'currency_code')
            ->value('option_value');

        if (! is_string($code) || trim($code) === '') {
            static::$cachedCurrencyCode = 'USD';

            return static::$cachedCurrencyCode;
        }

        static::$cachedCurrencyCode = strtoupper(trim($code));

        return static::$cachedCurrencyCode;
    }

    public static function currencySymbol(?string $currencyCode = null): string
    {
        if ($currencyCode === null && static::$cachedCurrencySymbol !== null) {
            return static::$cachedCurrencySymbol;
        }

        $code = strtoupper(trim($currencyCode ?: static::currencyCode()));

        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'INR' => '₹',
            'MXN' => '$',
            'BRL' => 'R$',
            'RUB' => '₽',
            'KRW' => '₩',
            'SGD' => 'S$',
            'HKD' => 'HK$',
            'NZD' => 'NZ$',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'PLN' => 'zł',
            'TRY' => '₺',
            'ZAR' => 'R',
            'AED' => 'د.إ',
            'SAR' => '﷼',
            'THB' => '฿',
            'MYR' => 'RM',
            'IDR' => 'Rp',
            'PHP' => '₱',
            'VND' => '₫',
            'ILS' => '₪',
            'EGP' => '£',
            'PKR' => '₨',
            'BDT' => '৳',
            'NGN' => '₦',
            'ARS' => '$',
            'CLP' => '$',
            'COP' => '$',
            'PEN' => 'S/',
            'CZK' => 'Kč',
            'HUF' => 'Ft',
            'RON' => 'lei',
            'BGN' => 'лв',
            'HRK' => 'kn',
            'ISK' => 'kr',
        ];

        $symbol = $symbols[$code] ?? '$';

        if ($currencyCode === null) {
            static::$cachedCurrencySymbol = $symbol;
        }

        return $symbol;
    }
}
