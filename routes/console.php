<?php

use App\Http\Controllers\SalonSettingsController;
use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clickaio:refresh-token', function () {
    $token = Setting::query()->where('option_key', 'clickaio_access_token')->value('option_value');
    $refreshToken = Setting::query()->where('option_key', 'clickaio_refresh_token')->value('option_value');

    if (! $token || ! $refreshToken) {
        $this->info('No Clickaio token to refresh (not authorized yet).');

        return;
    }

    try {
        SalonSettingsController::performClickaioRefresh();
        $this->info('Clickaio token refreshed successfully.');
    } catch (\Exception $e) {
        $this->error('Failed to refresh Clickaio token: '.$e->getMessage());
        Log::error('Clickaio scheduled token refresh failed: '.$e->getMessage());
    }
})->purpose('Refresh the GoHighLevel (Clickaio) access token');

Schedule::command('clickaio:refresh-token')->hourly();
