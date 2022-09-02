<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\DataObject;

use YaangVu\LaravelBase\Enums\CastEnum;

class Cast
{
    private string   $column;
    private CastEnum $type;

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
     * @return Cast
     */
    public function setColumn(string $column): Cast
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return CastEnum
     */
    public function getType(): CastEnum
    {
        return $this->type;
    }

    /**
     * @param CastEnum $type
     *
     * @return Cast
     */
    public function setType(CastEnum $type): Cast
    {
        $this->type = $type;

        return $this;
    }
}
