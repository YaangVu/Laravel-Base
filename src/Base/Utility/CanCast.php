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
    private array $casts;

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
    public function cast(mixed $value, CastEnum $type = null): float|int|Carbon|string
    {
        return match ($type) {
            CastEnum::DATE => Carbon::parse($value)->toDateString(),
            CastEnum::DATETIME => Carbon::parse($value),
            CastEnum::FLOAT => (float)$value,
            CastEnum::NUMBER,
            CastEnum::LONG,
            CastEnum::DOUBLE => (double)$value,
            CastEnum::INT => (int)$value,
            CastEnum::STRING => trim((string)$value),
            default => $value
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
     * @return CastEnum
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
}
