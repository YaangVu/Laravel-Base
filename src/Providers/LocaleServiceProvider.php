<?php

namespace YaangVu\LaravelBase\Providers;

use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $locale = \request()->header('locale') ?? env('APP_LOCALE', 'en');
        $this->app->setLocale($locale);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
