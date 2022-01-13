<?php
/**
 * @Author yaangvu
 * @Date   Jul 31, 2021
 */

namespace YaangVu\LaravelBase\Docs\Example;

use YaangVu\LaravelBase\Controllers\BaseController;

class ExampleController extends BaseController
{

    protected function initService()
    {
        $this->service = new ExampleService();
    }
}