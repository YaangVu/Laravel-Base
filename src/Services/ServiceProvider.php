<?php

namespace YaangVu\LaravelBase\Services;

use YaangVu\LaravelBase\Constants\DbDriverConstant;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/laravel-base.php';
        $this->mergeConfigFrom($configPath, 'laravel-base');
        // $driver = config('laravel-base.driver');

        $this->app->bind('base.service', function ($app) {
            return new Facade($app['config']->get('laravel-base.driver') ?? 'mysql');
        });
    }

    /**
     * @Author yaangvu
     * @Date   Jul 26, 2021
     *
     * @return array
     */
    public function provides(): array
    {
        return DbDriverConstant::ALL;
    }
}
