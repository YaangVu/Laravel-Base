<?php
/**
 * @Author yaangvu
 * @Date   Jul 29, 2021
 */

namespace YaangVu\LaravelBase\Helpers;


use YaangVu\LaravelBase\Constants\OperatorConstant;

class PgsqlQueryHelper extends QueryHelper
{
    public function setOperators(array $operators = []): static
    {
        $this->operators                                 = OperatorConstant::DEFAULT_OPERATORS;
        $this->operators[OperatorConstant::LIKE_PATTERN] = OperatorConstant::I_LIKE;

        return $this;
    }
}