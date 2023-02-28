<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\DataObject;

use YaangVu\LaravelBase\Base\Enum\CastEnum;

class Cast
{
    private string   $key;
    private CastEnum $type;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Cast
     */
    public function setKey(string $key): Cast
    {
        $this->key = $key;

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
