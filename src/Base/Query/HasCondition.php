<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use YaangVu\LaravelBase\Base\Clauses\Condition;

trait HasCondition
{
    /**
     * @var Condition[]
     */
    private array $conditions = [];

    /**
     * @var Condition[]
     */
    private array $excludedConditions = [];

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
     * @return HasCondition
     */
    public function setConditions(array $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @return Condition[]
     */
    public function getExcludedConditions(): array
    {
        return $this->excludedConditions;
    }

    /**
     * @param Condition[] $excludedConditions
     *
     * @return HasCondition
     */
    public function setExcludedConditions(array $excludedConditions): static
    {
        $this->excludedConditions = $excludedConditions;

        return $this;
    }

    /**
     *  Add more condition
     *
     * @Author      yaangvu
     * @Date        Jul 26, 2022
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

    /**
     *  Remove condition
     *
     * @Author yaangvu
     * @Date   Jul 27, 2022
     *
     * @param string $column
     *
     * @return $this
     */
    public function removeCondition(string $column): static
    {
        $this->conditions = array_filter($this->conditions, function (Condition $condition) use ($column) {
            return $condition->getColumn() != $column;
        });

        return $this;
    }

    /**
     *  Add more exclude condition
     *
     * @Author      yaangvu
     * @Date        Jul 26, 2022
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function excludeCondition(Condition $condition): static
    {
        $this->excludedConditions[] = $condition;

        return $this;
    }

    /**
     *  Remove excluded condition
     *
     * @Author yaangvu
     * @Date   Jul 27, 2022
     *
     * @param string $column
     *
     * @return $this
     */
    public function removeExcludedCondition(string $column): static
    {
        $this->excludedConditions =
            array_filter($this->excludedConditions, function (Condition $condition) use ($column) {
                return $condition->getColumn() != $column;
            });

        return $this;
    }
}
