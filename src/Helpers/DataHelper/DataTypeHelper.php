<?php
/**
 * @Author yaangvu
 * @Date   Jan 12, 2022
 */

namespace YaangVu\LaravelBase\Helpers\DataHelper;

use Carbon\Carbon;
use JetBrains\PhpStorm\Pure;
use YaangVu\LaravelBase\Enumerations\DataTypeEnum;

class DataTypeHelper
{
    private array $params;

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
     * @param string       $param
     * @param DataTypeEnum $type
     *
     * @return $this
     */
    public function addParam(string $param, DataTypeEnum $type): static
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
     * @param mixed        $value
     * @param DataTypeEnum $type
     *
     * @return float|int|Carbon|string
     */
    public function cast(mixed $value, DataTypeEnum $type): float|int|Carbon|string
    {
        return match ($type) {
            DataTypeEnum::DATE => Carbon::createFromDate($value),
            DataTypeEnum::DATETIME => Carbon::parse($value),
            DataTypeEnum::FLOAT => (float)$value,
            DataTypeEnum::LONG, DataTypeEnum::DOUBLE => (double)$value,
            DataTypeEnum::INT => (int)$value,
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
     * @return DataTypeEnum
     */
    #[Pure]
    public function getType(string $param): DataTypeEnum
    {
        $type = DataTypeEnum::STRING;
        foreach ($this->getParams() as $dataType) {
            if ($param == $dataType->getParam())
                $type = $dataType->getType();
        }

        return $type;
    }
}

class DataType
{
    private string       $param;
    private DataTypeEnum $type;

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
     * @return DataTypeEnum
     */
    public function getType(): DataTypeEnum
    {
        return $this->type;
    }

    /**
     * @param DataTypeEnum $type
     */
    public function setType(DataTypeEnum $type): void
    {
        $this->type = $type;
    }
}