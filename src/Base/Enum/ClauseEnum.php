<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Enum;

enum ClauseEnum: string
{
    case SELECT = 'select';
    case LIMIT = 'limit';
    case PAGE = 'page';
    case SORT = 'sort';
}
