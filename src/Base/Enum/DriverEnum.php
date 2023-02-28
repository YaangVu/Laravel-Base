<?php
/**
 * @Author yaangvu
 * @Date   Aug 05, 2022
 */

namespace YaangVu\LaravelBase\Base\Enum;

use YaangVu\LaravelBase\Base\Utility\EnumToArray;

enum DriverEnum: string
{
    use EnumToArray;
    case MYSQL = 'mysql';
    case POSTGRES = 'pgsql';
    case MONGODB = 'mongodb';
    case SQLSRV = 'sqlsrv';
    case SQLITE = 'sqlite';
}
