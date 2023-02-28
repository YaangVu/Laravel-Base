<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Enum;

enum CastEnum: string
{
    case DATE = 'date';
    case DATETIME = 'datetime';
    case NUMBER = 'number';
    case DOUBLE = 'double';
    case LONG = 'long';
    case FLOAT = 'float';
    case INT = 'int';
    case STRING = 'string';
}
