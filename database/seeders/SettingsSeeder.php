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
            'payment_authorize_net_enabled' => true,
            'payment_authorize_net_api_login_id' => '',
            'payment_authorize_net_transaction_key' => '',
            'payment_authorize_net_tokenization' => true,
            'payment_authorize_net_environment' => 'sandbox',
            'payment_nmi_enabled' => false,
            'payment_nmi_username' => '',
            'payment_nmi_password' => '',
            'payment_nmi_fraud_detection' => true,
            'tax_rate' => '5.00',
            'tax_name' => 'Sales Tax',
            'tax_apply_to_all' => true,
            'currency_code' => 'USD',
            'turn_tracker_order' => 'lowest',
            'discounts_enabled' => true,
            'gift_cards_enabled' => true,
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
