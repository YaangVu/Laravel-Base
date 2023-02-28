<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Enum;

enum OperatorPatternEnum: string
{
    case GT = 'gt';
    case GE = 'ge';
    case LT = 'lt';
    case LE = 'le';
    case LIKE = '~';
    case EQUAL = 'eq';
}
