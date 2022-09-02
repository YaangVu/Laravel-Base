<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Providers;

use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RequestServiceProvider::class);

        $this->app->register(ConditionMakerServiceProvider::class);

        $this->app->register(RouterServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $configPath = __DIR__ . '/../config/laravel-base.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');
        $this->mergeConfigFrom($configPath, 'laravel-base');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return config_path('laravel-base.php');
    }

    /**
     * Publish the config file
     *
     * @param string $configPath
     */
    protected function publishConfig(string $configPath)
    {
        $this->publishes([$configPath => config_path('laravel-base.php')], 'config');
    }
}
