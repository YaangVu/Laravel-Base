<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Facades;

use Illuminate\Support\Facades\Facade;
use YaangVu\LaravelBase\Base\Clauses\LikeConditionMaker\Maker;
use YaangVu\LaravelBase\Base\Clauses\LikeConditionMaker\MongoMaker;
use YaangVu\LaravelBase\Base\Clauses\LikeConditionMaker\MySQLMaker;
use YaangVu\LaravelBase\Base\Clauses\LikeConditionMaker\PostgresMaker;
use YaangVu\LaravelBase\Base\Enums\DbDriverEnum;
use YaangVu\LaravelBase\Base\Enums\OperatorEnum;

/**
 * @method static OperatorEnum like();
 * @method static mixed value(mixed $value);
 *
 * @see Maker
 */
class LikeConditionMaker extends Facade
{
    /**
     * Retrieve instance of Maker by database driver
     *
     * @Author yaangvu
     * @Date   Aug 25, 2022
     *
     * @param string|null $driver
     *
     * @return Maker
     */
    public static function make(?string $driver = null): Maker
    {
        $driver = $driver ?? config('database.default');

        return match ($driver) {
            DbDriverEnum::POSTGRES => new PostgresMaker(),
            DbDriverEnum::MONGODB => new MongoMaker(),
            default => new MySQLMaker()
        };
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'LikeConditionMaker';
    }
}
