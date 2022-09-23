<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Enums;

use YaangVu\LaravelBase\Helpers\EnumToArray;

enum OperatorEnum: string
{
    use EnumToArray;

    case GT = '>';
    case GE = '>=';
    case LT = '<';
    case LE = '<=';
    case LIKE = 'like';
    case EQUAL = '=';
    case I_LIKE = 'ilike'; // specific for postgresql
}
