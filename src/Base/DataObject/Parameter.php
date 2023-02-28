<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\DataObject;

class Parameter
{
    private string $key;

    private mixed $value;

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
     * @return Parameter
     */
    public function setKey(string $key): Parameter
    {
        $this->key = $key;

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
     * @return Parameter
     */
    public function setValue(mixed $value): Parameter
    {
        $this->value = $value;

        return $this;
    }
}