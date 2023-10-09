<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Enum;

enum ClauseEnum: string
{
    case SELECT     = 'select';
    case LIMIT      = 'limit';
    case PAGE       = 'page';
    case SORT       = 'sort';
    case KEYWORD    = 'keyword';
    case WITH       = 'with';
    case WITH_COUNT = 'with_count';
    case WITH_SUM   = 'with_sum';
    case WITH_AVG   = 'with_avg';
    case WITH_MIN   = 'with_min';
    case WITH_MAX   = 'with_max';
}
