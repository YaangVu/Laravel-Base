<?php
/**
 * @Author yaangvu
 * @Date   Feb 23, 2023
 */

namespace YaangVu\LaravelBase\Base\Operator;

use YaangVu\LaravelBase\Base\Contract\Operator;
use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;

class MysqlOperator implements Operator
{

    public function make(OperatorPatternEnum $pattern): string
    {
        return match ($pattern) {
            OperatorPatternEnum::EQUAL => '=',
            OperatorPatternEnum::GT => '>',
            OperatorPatternEnum::GE => '>=',
            OperatorPatternEnum::LT => '<',
            OperatorPatternEnum::LE => '<=',
            OperatorPatternEnum::LIKE => 'LIKE',
        };
    }
}