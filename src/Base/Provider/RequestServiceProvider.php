<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Provider;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        Request::macro('serialize', function () {
            $serialization = '';
            foreach (\request()->toArray() as $key => $value)
                $serialization .= ":$key:$value";

            return trim($serialization, ':');
        });

        Request::macro('toJson', function () {
            return json_encode(\request()->toArray());
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
