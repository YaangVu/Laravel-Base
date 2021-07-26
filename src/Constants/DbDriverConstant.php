<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2021
 */

namespace YaangVu\LaravelBase\Constants;


class DbDriverConstant
{
    const MYSQL    = 'mysql';
    const POSTGRES = 'pgsql';
    const MONGODB  = 'mongodb';

    const ALL = [self::MYSQL, self::POSTGRES, self::MONGODB];
}