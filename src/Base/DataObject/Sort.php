<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\DataObject;

class Sort
{
    private string $table;
    private string $column;
    /**
     * @var string $type accept 2 values: ASC, DESC
     */
    private string $type;

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return Sort
     */
    public function setTable(string $table): Sort
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
     * @return Sort
     */
    public function setColumn(string $column): Sort
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Sort
     */
    public function setType(string $type): Sort
    {
        $this->type = $type;

        return $this;
    }
}
