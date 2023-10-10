<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\Utility;

use Carbon\Carbon;
use YaangVu\LaravelBase\Base\DataObject\Cast;
use YaangVu\LaravelBase\Base\Enum\CastEnum;

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
     * @param mixed         $value
     * @param CastEnum|null $type
     *
     * @return mixed
     */
    public function cast(mixed $value, ?CastEnum $type = null): mixed
    {
        return match ($type) {
            CastEnum::DATE     => Carbon::parse($value)->toDateString(),
            CastEnum::DATETIME => Carbon::parse($value),
            CastEnum::FLOAT    => (float)$value,
            CastEnum::NUMBER,
            CastEnum::LONG,
            CastEnum::DOUBLE   => (double)$value,
            CastEnum::INT      => (int)$value,
            CastEnum::STRING   => trim((string)$value),
            default            => $value
        };
    }

    /**
     * Get data type will be cast
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param string $key
     *
     * @return CastEnum|null
     */
    public function getCastType(string $key): ?CastEnum
    {
        foreach ($this->getCasts() as $cast)
            if ($cast->getKey() === $key)
                return $cast->getType();

        return null;
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
     * Add more cast
     *
     * @Author      yaangvu
     * @Date        Mar 03, 2023
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
