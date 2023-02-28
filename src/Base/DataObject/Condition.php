<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\DataObject;

use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;

class Condition
{
    private ?string             $table = null;
    private string              $column;
    private OperatorPatternEnum $operatorPattern;
    private mixed               $value;

    /**
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @param string|null $table
     *
     * @return Condition
     */
    public function setTable(?string $table): Condition
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param string $column
     *
     * @return Condition
     */
    public function setColumn(string $column): Condition
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return OperatorPatternEnum
     */
    public function getOperatorPattern(): OperatorPatternEnum
    {
        return $this->operatorPattern;
    }

    /**
     * @param OperatorPatternEnum $operatorPattern
     *
     * @return Condition
     */
    public function setOperatorPattern(OperatorPatternEnum $operatorPattern): Condition
    {
        $this->operatorPattern = $operatorPattern;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Condition
     */
    public function setValue(mixed $value): Condition
    {
        $this->value = $value;

        return $this;
    }
}
