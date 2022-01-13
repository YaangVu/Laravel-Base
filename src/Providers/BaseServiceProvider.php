<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2021
 */

namespace YaangVu\LaravelBase\Providers;

use Illuminate\Support\ServiceProvider;
use YaangVu\LaravelBase\Constants\DbDriverConstant;
use YaangVu\LaravelBase\Helpers\QueryHelper\MongoQueryHelper;
use YaangVu\LaravelBase\Helpers\QueryHelper\MysqlQueryHelper;
use YaangVu\LaravelBase\Helpers\QueryHelper\PgsqlQueryHelper;

class BaseServiceProvider extends ServiceProvider
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
        $this->config();

        $this->app->bind('query', function ($app) {
            return match ($app['db']->connection()->getDriverName()) {
                DbDriverConstant::POSTGRES => new PgsqlQueryHelper(),
                DbDriverConstant::MONGODB => new MongoQueryHelper(),
                default => new MysqlQueryHelper(),
            };
        });
    }

    public function config()
    {
        $configPath = __DIR__ . '/../config/laravel-base.php';
        $this->mergeConfigFrom($configPath, 'laravel-base');
    }
}
