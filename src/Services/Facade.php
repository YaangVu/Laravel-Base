<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2021
 */

namespace YaangVu\LaravelBase\Services;


use Illuminate\Support\Str;
use RuntimeException;
use YaangVu\LaravelBase\Constants\DbDriverConstant;

/**
 * Class Facade
 * @package YaangVu\LaravelBase\Services
 * @method static string helloWorld()
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @var string
     */
    static string $accessor = 'mysql';

    /**
     * Facade constructor.
     *
     * @param string $driver
     */
    public function __construct(string $driver = 'mysql')
    {
        if (in_array($driver, DbDriverConstant::ALL))
            self::$accessor = Str::ucfirst($driver) . 'BaseService';
        else
            self::$accessor = 'BaseService';
    }

    /**
     * @Author yaangvu
     * @Date   Jul 26, 2021
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'MysqlBaseService';
    }

    /**
     * @Author yaangvu
     * @Date   Jul 26, 2021
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return match (count($args)) {
            0 => $instance->$method(),
            1 => $instance->$method($args[0]),
            2 => $instance->$method($args[0], $args[1]),
            3 => $instance->$method($args[0], $args[1], $args[2]),
            4 => $instance->$method($args[0], $args[1], $args[2], $args[3]),
            default => call_user_func_array([$instance, $method], $args),
        };
    }
}