<?php


namespace YaangVu\LaravelBase\Helpers;


class RouterHelper
{
    /**
     * @param \Laravel\Lumen\Routing\Router $router
     * @param string                        $name
     * @param string                        $controller
     */
    public static function resource($router, string $name, string $controller): void
    {
        $router->get("$name", "$controller@index");
        $router->get("$name/{id}", "$controller@show");
        $router->post("$name", "$controller@store");
        $router->put("$name/{id}", "$controller@update");
        $router->patch("$name/{id}", "$controller@update");
        $router->delete("$name/{id}", "$controller@destroy");
        $router->delete("$name/code/{code}", "$controller@deleteByCode");
        $router->get("$name/code/{code}", "$controller@showByCode");
        $router->patch("$name/deletes", "$controller@deleteByIds");
        $router->patch("$name/deletes/code", "$controller@deleteByCodes");
    }
}
