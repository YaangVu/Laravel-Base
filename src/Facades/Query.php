<?php
/**
 * @Author yaangvu
 * @Date   Jul 28, 2021
 */

namespace YaangVu\LaravelBase\Facades;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use YaangVu\LaravelBase\Constants\DbDriverConstant;
use YaangVu\LaravelBase\Helpers\QueryHelper\MysqlQueryHelper;
use YaangVu\LaravelBase\Helpers\QueryHelper\QueryHelper;

/**
 * @see QueryHelper
 * @see MysqlQueryHelper
 * @see PgsqlQueryHelper
 * @property string $separator
 * @property array  $operatorPatterns
 * @property array  $operators
 * @property array  $excludedOperators
 * @property array  $castParams
 * @property array  $params
 * @property array  $relations
 * @method static array getOperators()
 * @method static QueryHelper setOperators(array $operators = [])
 * @method static QueryHelper setOperatorPatterns(array $operatorPatterns = [])
 * @method static array getParams()
 * @method static QueryHelper setParams(array $params = [])
 * @method static QueryHelper addParam(string $param, mixed $value)
 * @method static QueryHelper removeParam(string $param)
 * @method static array getCastParams()
 * @method static QueryHelper setCastParams(?array $params = null)
 * @method static QueryHelper addCastParam(string $param, mixed $type)
 * @method static QueryHelper removeCastParam(string $param)
 * @method static array getExcludedOperators()
 * @method static QueryHelper setExcludedOperators(?array $operators = null)
 * @method static QueryHelper addExcludedOperators(...$operator)
 * @method static QueryHelper removeExcludedOperators(string $operator)
 * @method static array getConditions(array $params = [])
 * @method static integer  limit()
 * @method static null|array getOrderBy()
 * @method static Builder|Model buildQuery(Model|Builder $model, string $alias = '')
 * @method static QueryHelper with(...$relations)
 * @method static array getRelations()
 */
class Query extends Facade
{
    static function getFacadeAccessor(): string
    {
        return 'query';
    }

    /**
     * @Author yaangvu
     * @Date   Jul 31, 2021
     *
     * @param string|null $driver
     *
     * @return mixed
     */
    public static function driver(?string $driver): mixed
    {
        if (!in_array($driver, DbDriverConstant::ALL))
            throw new \RuntimeException("Database driver was not found or not supported");

        $class = '\\YaangVu\\LaravelBase\\Helpers\\QueryHelper\\' . Str::studly($driver) . 'QueryHelper';

        if (class_exists($class))
            return new $class();
        else
            return new MysqlQueryHelper();
    }
}
