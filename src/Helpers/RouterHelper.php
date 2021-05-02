<?php


namespace YaangVu\LaravelBase\Helpers;


class RouterHelper
{
    public static function resource($router, string $name, string $controller, array $options = []): void
    {
        $router->get("$name", "$controller@index");
        $router->get("$name/{id}", "$controller@show");
        $router->post("$name", "$controller@store");
        $router->put("$name/{id}", "$controller@update");
        $router->delete("$name/{id}", "$controller@destroy");
    }
}