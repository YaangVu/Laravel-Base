<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2021
 */

namespace YaangVu\LaravelBase\Services;


use YaangVu\LaravelBase\Models\Example;

class ExampleService extends BaseService
{

    public function createModel(): void
    {
        $this->model = new Example();
    }
}