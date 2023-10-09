<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

use YaangVu\LaravelBase\Base\Enum\DriverEnum;
use YaangVu\LaravelBase\Base\Operator\MongodbOperator;
use YaangVu\LaravelBase\Base\Operator\MysqlOperator;
use YaangVu\LaravelBase\Base\Operator\PostgresOperator;
use YaangVu\LaravelBase\Base\Operator\SqliteOperator;
use YaangVu\LaravelBase\Base\Operator\SqlsrvOperator;

return [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'query'      => [
        'separator'      => '__',
        'limit'          => 10,
        'nullable_value' => false
    ],
    'cache'      => ['ttl' => 86400 /* 1 day */],
    'generator'  => ['rootNamespace' => 'Domains\\'],
    'operator'   => [
        DriverEnum::MONGODB->value  => MongodbOperator::class,
        DriverEnum::MYSQL->value    => MysqlOperator::class,
        DriverEnum::POSTGRES->value => PostgresOperator::class,
        DriverEnum::SQLITE->value   => SqliteOperator::class,
        DriverEnum::SQLSRV->value   => SqlsrvOperator::class,
    ]
];
