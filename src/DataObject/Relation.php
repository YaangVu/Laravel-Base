<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\DataObject;

class Relation
{
    private string|array $relation;
    private string       $column;

    /**
     * @return array|string
     */
    public function getRelation(): array|string
    {
        return $this->relation;
    }

    /**
     * @param array|string $relation
     *
     * @return Relation
     */
    public function setRelation(array|string $relation): Relation
    {
        $this->relation = $relation;

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
     * @return Relation
     */
    public function setColumn(string $column): Relation
    {
        $this->column = $column;

        return $this;
    }
}
