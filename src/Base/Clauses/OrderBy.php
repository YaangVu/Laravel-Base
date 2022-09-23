<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\Clauses;

class OrderBy
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
     * @return OrderBy
     */
    public function setTable(string $table): OrderBy
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
     * @return OrderBy
     */
    public function setColumn(string $column): OrderBy
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
     * @return OrderBy
     */
    public function setType(string $type): OrderBy
    {
        $this->type = $type;

        return $this;
    }
}
