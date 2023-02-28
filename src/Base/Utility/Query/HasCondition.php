<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Utility\Query;

use YaangVu\LaravelBase\Base\DataObject\Condition;
use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;
use YaangVu\LaravelBase\Base\Facade\Param;

trait HasCondition
{
    /**
     * @var Condition[]
     */
    private array $conditions;

    /**
     * @var string Separator
     */
    private string $separator;

    /**
     * The value of parameter can be null or not
     *
     * @var bool $nullableValue
     */
    private bool $nullableValue;

    /**
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @param Condition[] $conditions
     *
     * @return static
     */
    public function setConditions(array $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @Description Parse request param to condition, used to query into database
     *
     * @Author      yaangvu
     * @Date        Feb 21, 2023
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function parseCondition(string $key, mixed $value): static
    {
        // If $value is empty or null and can not be null then return
        if (($value === '' || $value === null) && $this->nullable() === false)
            return $this;

        switch (substr_count($key, $this->getSeparator())) {
            // If $query has formatted like: ${column}__${operator}
            case 1:
                [$table, $column, $operatorValue] = [null, ...explode($this->getSeparator(), $key)];
                break;

            // If $query has formatted like: ${table}__${column}__${operator}
            case 2:
                [$table, $column, $operatorValue] = explode($this->getSeparator(), $key);
                break;

            // If $query has formatted like: ${column}
            default:
                $table         = null;
                $column        = $key;
                $operatorValue = OperatorPatternEnum::EQUAL->value;
                break;
        }

        $operatorPattern = OperatorPatternEnum::from($operatorValue);

        // If $column is one of $excludedKeys then return
        if (in_array($column, Param::getExcludedKeys()))
            return $this;


        $condition = new Condition();
        $condition->setTable($table)
                  ->setColumn(($table ? "$table." : '') . $column)
                  ->setValue($value)
                  ->setOperatorPattern($operatorPattern);

        return $this->addCondition($condition);
    }

    /**
     * @Description Nullable parameter value
     *
     * @Author      yaangvu
     * @Date        Feb 28, 2023
     *
     * @return bool
     */
    private function nullable(): bool
    {
        return $this->isNullableValue();
    }

    /**
     * @return bool
     */
    public function isNullableValue(): bool
    {
        return $this->nullableValue;
    }

    /**
     * @param bool $nullableValue
     *
     * @return static
     */
    public function setNullableValue(bool $nullableValue): static
    {
        $this->nullableValue = $nullableValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     *
     * @return static
     */
    public function setSeparator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @Description Add more condition
     *
     * @Author      yaangvu
     * @Date        Feb 28, 2023
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function addCondition(Condition $condition): static
    {
        $this->conditions[] = $condition;

        return $this;
    }
}