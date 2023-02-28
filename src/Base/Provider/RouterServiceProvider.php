<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Provider;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        Route::macro('base', function (string $name, string $controller) {
            $routeNameGroup = str_replace('/', '', trim($name, '/'));
            Route::name("base.api.$routeNameGroup.")->group(function () use ($name, $controller) {
                Route::get("$name", "$controller@index")->name('index');
                Route::get("$name/{id}", "$controller@show")->name('show');
                Route::post("$name", "$controller@store")->name('store');
                Route::put("$name/{id}", "$controller@update")->name('put-update');
                Route::patch("$name/{id}", "$controller@update")->name('update');
                Route::delete("$name/{id}", "$controller@destroy")->name('destroy');

                // Add more route
                Route::get("$name/uuid/{uuid}", "$controller@showByUuid")->name('show-by-uuid');
                Route::delete("$name/uuid/{uuid}", "$controller@deleteByUuid")->name('destroy-by-uuid');
                Route::patch("$name/delete/ids", "$controller@deleteByIds")->name('delete-by-ids');
                Route::patch("$name/delete/uuids", "$controller@deleteByUuids")->name('delete-by-uuids');
            });
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
