<?php
/**
 * @Author yaangvu
 * @Date   Jul 31, 2021
 */

namespace YaangVu\LaravelBase\Controllers;

use YaangVu\LaravelBase\Services\ExampleService;

class ExampleController extends BaseController
{

    protected function createService()
    {
        $this->service = new ExampleService();
    }
}