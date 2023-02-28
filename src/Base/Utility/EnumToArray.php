<?php
/**
 * @Author yaangvu
 * @Date   Aug 05, 2022
 */

namespace YaangVu\LaravelBase\Base\Utility;

trait EnumToArray
{
    /**
     * Convert the enum to array
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @return array
     */
    public static function toArray(): array
    {
        return array_combine(self::names(), self::values());
    }

    /**
     * Get list names of the enum
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get list value of the enum
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

