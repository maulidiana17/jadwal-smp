<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

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

        if (app()->isProduction()) {
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');

        // Fix untuk pagination URL di deploy server seperti Coolify
        request()->server->set('HTTPS', 'on');
        }

        // Set default timezone
        Config::set('app.timezone', 'Asia/Jakarta');
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setLocale('id');
    }
}
