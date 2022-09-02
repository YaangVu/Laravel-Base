<?php
/**
 * @Author yaangvu
 * @Date   Aug 25, 2022
 */

namespace YaangVu\LaravelBase\ConditionMaker;

use YaangVu\LaravelBase\Helpers\CanCast;

abstract class NoSqlMaker implements Maker
{
    use CanCast;

    /**
     * @inheritDoc
     */
    public function value(mixed $value): string
    {
        return "%" . $this->cast($value) . "%";
    }
}
