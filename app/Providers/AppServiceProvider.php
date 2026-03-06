<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                View::share('currencySymbol', Setting::currencySymbol());

                $timezone = Setting::query()
                    ->where('option_key', 'timezone')
                    ->value('option_value');

                if ($timezone && in_array($timezone, timezone_identifiers_list())) {
                    config(['app.timezone' => $timezone]);
                    date_default_timezone_set($timezone);
                }
            } else {
                View::share('currencySymbol', '$');
            }
        } catch (\Throwable) {
            View::share('currencySymbol', '$');
        }
    }
}
