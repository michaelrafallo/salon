<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'business_name' => 'Lucky Nail Salon',
            'business_phone' => '',
            'business_email' => '',
            'business_address' => '',
            'receipt_print_enabled' => true,
            'receipt_include_business_info' => true,
            'tax_rate' => '5.00',
            'tax_name' => 'Sales Tax',
            'tax_apply_to_all' => true,
            'currency_code' => 'USD',
            'commission_rate' => '30.00',
            'timezone' => 'America/New_York',
            'turn_tracker_order' => 'lowest',
            'discounts_enabled' => true,
            'gift_cards_enabled' => true,
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
