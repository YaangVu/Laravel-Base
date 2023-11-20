<?php

namespace YaangVu\LaravelBase\Base\DataObject;

class Relation
{
    private string  $relation;
    private ?string $column;

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): Relation
    {
        $this->relation = $relation;

        return $this;
    }

    public function getColumn(): ?string
    {
        return $this->column;
    }

    public function setColumn(?string $column): Relation
    {
        $this->column = $column;

        return $this;
    }

}