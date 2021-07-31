<?php
/**
 * @Author yaangvu
 * @Date   Jul 30, 2021
 */

namespace YaangVu\LaravelBase\Facades;

use Illuminate\Support\Facades\Facade;

class File extends Facade
{
    static function getFacadeAccessor(): string
    {
        return 'file';
    }
}