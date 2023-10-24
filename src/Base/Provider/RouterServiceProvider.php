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
        Route::macro('base', function (string $uri, string $controller, ?string $name = null) {
            $routeNameGroup = $name ?? str_replace('/', '', trim($uri, '/'));
            Route::name("base.api.$routeNameGroup.")->group(function () use ($uri, $controller) {
                Route::get("$uri", "$controller@index")->name('index');
                Route::get("$uri/{id}", "$controller@show")->name('show');
                Route::post("$uri", "$controller@store")->name('store');
                Route::put("$uri/{id}", "$controller@update")->name('put-update');
                Route::patch("$uri/{id}", "$controller@update")->name('update');
                Route::delete("$uri/{id}", "$controller@destroy")->name('destroy');

                // Add more route
                Route::get("$uri/uuid/{uuid}", "$controller@showByUuid")->name('show-by-uuid');
                Route::delete("$uri/uuid/{uuid}", "$controller@deleteByUuid")->name('destroy-by-uuid');
                Route::patch("$uri/delete/ids", "$controller@deleteByIds")->name('delete-by-ids');
                Route::patch("$uri/delete/uuids", "$controller@deleteByUuids")->name('delete-by-uuids');
            });

            return $this;
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
