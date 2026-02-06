<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')
            ->whereIn('option_key', [
                'payment_authorize_net_enabled',
                'payment_authorize_net_api_login_id',
                'payment_authorize_net_transaction_key',
                'payment_authorize_net_tokenization',
                'payment_authorize_net_environment',
                'payment_nmi_enabled',
                'payment_nmi_username',
                'payment_nmi_password',
                'payment_nmi_fraud_detection',
            ])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $defaults = [
            'payment_authorize_net_enabled' => '1',
            'payment_authorize_net_api_login_id' => '',
            'payment_authorize_net_transaction_key' => '',
            'payment_authorize_net_tokenization' => '1',
            'payment_authorize_net_environment' => 'sandbox',
            'payment_nmi_enabled' => '0',
            'payment_nmi_username' => '',
            'payment_nmi_password' => '',
            'payment_nmi_fraud_detection' => '1',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['option_key' => $key],
                ['option_value' => $value, 'updated_at' => now()]
            );
        }
    }
};
