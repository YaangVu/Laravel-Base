<?php
/**
 * @Author yaangvu
 * @Date   Feb 23, 2023
 */

namespace YaangVu\LaravelBase\Base\Contract;

use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;

interface Operator
{
    /**
     * Make operator for query builder
     *
     * @param OperatorPatternEnum $pattern
     *
     * @return string
     */
    public function make(OperatorPatternEnum $pattern): string;
}