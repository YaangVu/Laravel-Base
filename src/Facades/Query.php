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
use YaangVu\LaravelBase\Helpers\PgsqlQueryHelper;
use YaangVu\LaravelBase\Helpers\QueryHelper;

/**
 * @property array  $conditions
 * @property string $name
 * @method static array getOperators()
 * @method static QueryHelper setOperators(array $operators = [])
 * @method static QueryHelper setOperatorPatterns(array $operatorPatterns = [])
 * @method static array getParams()
 * @method static QueryHelper setParams(array $params = [])
 * @method static QueryHelper addParam(array $param)
 * @method static QueryHelper removeParam(string $param)
 * @method static array getCastParams()
 * @method static QueryHelper setCastParams(?array $params = null)
 * @method static QueryHelper addCastParam(array $castParam)
 * @method static QueryHelper removeCastParam(string $castParam)
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
 *
 * @see QueryHelper
 * @see MysqlQueryHelper
 * @see PgsqlQueryHelper
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
     * @param string $driver
     *
     * @return static
     */
    public static function driver(string $driver): static
    {
        if (!in_array($driver, DbDriverConstant::ALL))
            throw new \RuntimeException("Database driver was not found or not supported");

        $class = '\\YaangVu\\LaravelBase\\Helpers\\' . $driver . 'QueryHelper';

        return new $class();
    }
}
