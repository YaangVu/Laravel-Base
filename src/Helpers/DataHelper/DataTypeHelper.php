<?php
/**
 * @Author yaangvu
 * @Date   Jan 12, 2022
 */

namespace YaangVu\LaravelBase\Helpers\DataHelper;

use Carbon\Carbon;
use JetBrains\PhpStorm\Pure;
use YaangVu\LaravelBase\Constants\DataTypeConstant;

class DataTypeHelper
{
    private array $params = [];

    /**
     * @Description Get all params will be cast
     *
     * @Author      yaangvu
     * @Date        Jan 12, 2022
     *
     * @return DataType[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @Description add param to be cast
     *
     * @Author      yaangvu
     * @Date        Jan 12, 2022
     *
     * @param string $param
     * @param string $type
     *
     * @return $this
     */
    public function addParam(string $param, string $type): static
    {
        $dataType = new DataType();
        $dataType->setParam($param);
        $dataType->setType($type);
        $this->params[] = $dataType;

        return $this;
    }

    /**
     * @Description remove cast param
     *
     * @Author      yaangvu
     * @Date        Jan 12, 2022
     *
     * @param string $param
     */
    public function removeParam(string $param)
    {
        foreach ($this->getParams() as $key => $dataType) {
            if ($param == $dataType->getParam())
                unset($this->params[$key]);
        }
    }

    /**
     * @Description cast data to specification type
     *
     * @Author      yaangvu
     * @Date        Jan 12, 2022
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return float|int|Carbon|string
     */
    public function cast(mixed $value, string $type): float|int|Carbon|string
    {
        return match ($type) {
            DataTypeConstant::DATE => Carbon::createFromDate($value),
            DataTypeConstant::DATETIME => Carbon::parse($value),
            DataTypeConstant::FLOAT => (float)$value,
            DataTypeConstant::NUMBER,
            DataTypeConstant::LONG,
            DataTypeConstant::DOUBLE => (double)$value,
            DataTypeConstant::INT => (int)$value,
            default => trim((string)$value)
        };
    }

    /**
     * @Description
     *
     * @Author yaangvu
     * @Date   Jan 12, 2022
     *
     * @param string $param
     *
     * @return string
     */
    #[Pure]
    public function getType(string $param): string
    {
        $type = DataTypeConstant::STRING;
        foreach ($this->getParams() as $dataType) {
            if ($param == $dataType->getParam())
                $type = $dataType->getType();
        }

        return $type;
    }
}

class DataType
{
    private string $param;
    private string $type;

    /**
     * @return string
     */
    public function getParam(): string
    {
        return $this->param;
    }

    /**
     * @param string $param
     */
    public function setParam(string $param): void
    {
        $this->param = $param;
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
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}