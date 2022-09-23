<?php
/**
 * @Author yaangvu
 * @Date   Aug 05, 2022
 */

namespace YaangVu\LaravelBase\Base\Enums;

use YaangVu\LaravelBase\Helpers\EnumToArray;

enum DbDriverEnum: string
{
    use EnumToArray;

    case MYSQL = 'mysql';
    case POSTGRES = 'pgsql';
    case MONGODB = 'mongodb';
}
