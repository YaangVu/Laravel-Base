<?php


namespace YaangVu\LaravelBase\Helpers;


class RouterHelper
{
    /**
     * @param Router $router
     * @param string $name
     * @param string $controller
     */
    public static function resource($router, string $name, string $controller): void
    {
        // Get routes
        $router->get("$name", "$controller@index");
        $router->get("$name/{id}", "$controller@show");
        $router->get("$name/uuid/{uuid}", "$controller@showByCode");

        // Post routes
        $router->post("$name", "$controller@store");

        // Update routes
        $router->put("$name/{id}", "$controller@update");
        $router->patch("$name/{id}", "$controller@update");

        // Delete routes
        $router->delete("$name/{id}", "$controller@destroy");
        $router->delete("$name/uuid/{uuid}", "$controller@deleteByUuid");
        $router->patch("$name/delete/ids", "$controller@deleteByIds");
        $router->patch("$name/delete/uuids", "$controller@deleteByUuids");
    }
}
