<?php
/**
 * @Author yaangvu
 * @Date   Jul 29, 2021
 */

namespace YaangVu\LaravelBase\Helpers\QueryHelper;


use YaangVu\LaravelBase\Constants\OperatorConstant;

class PgsqlQueryHelper extends AbstractQueryHelper
{
    public function setOperators(array $operators = []): static
    {
        $this->operators                                 = OperatorConstant::DEFAULT_OPERATORS;
        $this->operators[OperatorConstant::LIKE_PATTERN] = OperatorConstant::I_LIKE;

        return $this;
    }
}