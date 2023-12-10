<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Provider;

use Illuminate\Support\ServiceProvider;
use YaangVu\LaravelBase\Base\Contract\Operator;
use YaangVu\LaravelBase\Base\Provider\RequestServiceProvider;
use YaangVu\LaravelBase\Base\Provider\RouterServiceProvider;

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
        $this->publishConfig();
        $this->publishBaseClasses();
        $this->publicLang();
        $this->mergeConfigFrom($configPath, 'laravel-base');

        $this->app->bind(Operator::class, function ($app, $params) {
            $operatorClass = config('laravel-base.operator.' . $params['driver']);

            return new $operatorClass();
        });
    }

    /**
     * Publish the config file
     */
    protected function publishConfig(): void
    {
        $configPath = __DIR__ . '/../config/laravel-base.php';
        $this->publishes([$configPath => config_path('laravel-base.php')], 'config');
    }

    public function publishBaseClasses(): void
    {
        // Publish BaseController
        $controllerPath = __DIR__ . '/../Base/Publish/Controller.php';
        $this->publishes([$controllerPath => app_path('Base/Controller.php')], 'base');

        // Publish BaseService
        $servicePath = __DIR__ . '/../Base/Publish/Service.php';
        $this->publishes([$servicePath => app_path('Base/Service.php')], 'base');
    }

    public function publicLang(): void
    {
        // Publish Lang folder
        $langPath = __DIR__ . '/../lang';
        $this->publishes([$langPath => resource_path('lang')], 'lang');
    }
}
