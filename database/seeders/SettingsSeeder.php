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
            'clickaio_client_id' => '',
            'clickaio_client_secret' => '',
            'clickaio_version' => '2.0.0',
            'clickaio_scopes' => 'contacts.readonly contacts.write locations/customFields.readonly locations/customFields.write locations/customValues.readonly locations/customValues.write opportunities.readonly opportunities.write',
            'clickaio_location_id' => '',
            'clickaio_calendar_id' => '',
            'clickaio_endpoint_find_contact' => 'https://services.leadconnectorhq.com/contacts/search',
            'clickaio_endpoint_create_contact' => 'https://services.leadconnectorhq.com/contacts/',
            'clickaio_endpoint_book_appointment' => 'https://services.leadconnectorhq.com/calendars/events/appointments',
            'clickaio_endpoint_delete_appointment' => 'https://services.leadconnectorhq.com/calendars/events/',
            'clickaio_access_token' => '',
            'clickaio_refresh_token' => '',
            'clickaio_token_expires_at' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
