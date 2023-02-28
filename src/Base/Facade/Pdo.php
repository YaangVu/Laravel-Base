<?php
/**
 * @Author yaangvu
 * @Date   Feb 23, 2023
 */

namespace YaangVu\LaravelBase\Base\Facade;

use YaangVu\LaravelBase\Base\Enum\DriverEnum;
use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;
use YaangVu\LaravelBase\Base\Operator\MongodbOperator;
use YaangVu\LaravelBase\Base\Operator\MysqlOperator;
use YaangVu\LaravelBase\Base\Operator\PostgresOperator;
use YaangVu\LaravelBase\Base\Operator\SqliteOperator;
use YaangVu\LaravelBase\Base\Operator\SqlsrvOperator;

class Pdo
{
    private static string $driver;

    public static function driver(string $driver): void
    {
        self::$driver = $driver;
    }

    public static function makeOperator(OperatorPatternEnum $pattern): string
    {
        $operatorMaker = match (self::$driver) {
            DriverEnum::POSTGRES->value => new PostgresOperator(),
            DriverEnum::MONGODB->value => new MongodbOperator(),
            DriverEnum::SQLITE->value => new SqliteOperator(),
            DriverEnum::SQLSRV->value => new SqlsrvOperator(),
            default => new MysqlOperator(),
        };

        return $operatorMaker->make($pattern);
    }
}