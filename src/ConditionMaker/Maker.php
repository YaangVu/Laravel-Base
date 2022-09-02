<?php
/**
 * @Author yaangvu
 * @Date   Aug 22, 2022
 */

namespace YaangVu\LaravelBase\ConditionMaker;

use YaangVu\LaravelBase\Enums\OperatorEnum;

interface Maker
{
    /**
     *
     *
     * @Author yaangvu
     * @Date   Aug 25, 2022
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function value(mixed $value): mixed;

    /**
     *
     *
     * @Author yaangvu
     * @Date   Aug 25, 2022
     *
     * @return OperatorEnum
     */
    public function like(): OperatorEnum;
}
