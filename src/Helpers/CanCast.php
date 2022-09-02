<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Helpers;

use Carbon\Carbon;
use YaangVu\LaravelBase\DataObject\Cast;
use YaangVu\LaravelBase\Enums\CastEnum;

trait CanCast
{
    /**
     * @var Cast[]
     */
    private array $casts = [];

    /**
     * Cast data beyond type
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param mixed    $value
     * @param CastEnum $type
     *
     * @return float|int|Carbon|string
     */
    public function cast(mixed $value, CastEnum $type = CastEnum::STRING): float|int|Carbon|string
    {
        return match ($type) {
            CastEnum::DATE => Carbon::parse($value)->toDateString(),
            CastEnum::DATETIME => Carbon::parse($value),
            CastEnum::FLOAT => (float)$value,
            CastEnum::NUMBER,
            CastEnum::LONG,
            CastEnum::DOUBLE => (double)$value,
            CastEnum::INT => (int)$value,
            default => trim((string)$value)
        };
    }

    /**
     * Get data type will be cast
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param string $column
     *
     * @return CastEnum
     */
    public function getCastType(string $column): CastEnum
    {
        foreach ($this->getCasts() as $cast)
            if ($column == $cast->getColumn())
                $type = $cast->getType();

        return $type ?? CastEnum::STRING;
    }

    /**
     * @return Cast[]
     */
    public function getCasts(): array
    {
        return $this->casts;
    }

    /**
     * @param Cast[] $casts
     *
     * @return CanCast
     */
    public function setCasts(array $casts): static
    {
        $this->casts = $casts;

        return $this;
    }

    /**
     * Add more cast param
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param Cast $cast
     *
     * @return $this
     */
    public function addCast(Cast $cast): static
    {
        $this->casts[] = $cast;

        return $this;
    }
}
