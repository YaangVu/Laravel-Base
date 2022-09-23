<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Providers;

use Illuminate\Support\ServiceProvider;
use YaangVu\LaravelBase\Base\Facades\LikeConditionMaker;

class ConditionMakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('LikeConditionMaker', function ($app) {
            return LikeConditionMaker::make(config('database.default'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
