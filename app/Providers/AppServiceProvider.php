<?php

namespace App\Providers;

use App\Models\BrightMLS\CompanyBrightOffices;
use App\Models\Config\Config;
use App\Models\HeritageFinancial\Loans;
use App\Observers\HeritageFinancial\LoansObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        date_default_timezone_set('America/New_York');

        // add custom config vars from config table
        // General config - string or array
        config([
            'global' => Config::all([
                'config_key', 'config_value', 'value_type',
            ])
            ->keyBy('config_key')
            ->transform(function ($setting) {
                if ($setting->value_type == 'array') {
                    return explode(',', $setting->config_value);
                }

                return $setting->config_value;
            })
            ->toArray(),
        ]);

        // custom configs / from tables other than config
        config([
            'bright_office_codes' => CompanyBrightOffices::all([
                'bright_office_code',
            ])
            ->transform(function ($setting) {
                if (stristr($setting->bright_office_code, ',')) {
                    return explode(',', $setting->bright_office_code);
                }

                return $setting->bright_office_code;
            })
            ->toArray(),
        ]);
    }
}
