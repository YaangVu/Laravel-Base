<?php

namespace YaangVu\LaravelBase\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use YaangVu\LaravelBase\Constants\DataCastConstant;
use YaangVu\LaravelBase\Constants\OperatorConstant;

class QueryHelper
{
    protected array $operatorPatterns;

    /**
     * Operators to query into DB
     * @var array
     */
    protected array $operators
        = [
            '__gt' => OperatorConstant::GT, // Greater than
            '__ge' => OperatorConstant::GE, // Greater than or equal
            '__lt' => OperatorConstant::LT, // Less than
            '__le' => OperatorConstant::LE, // Less than or equal
            '__~'  => OperatorConstant::LIKE // Like
        ];

    /**
     * Operators were excluded
     * @var array|string[]
     */
    protected array $excludedOperators
        = [
            'limit',
            'page',
            'order_by'
        ];

    /**
     * Params will be cast to data type
     * @var array
     */
    protected array $castParams
        = [
            'date'       => DataCastConstant::DATE,
            'created_at' => DataCastConstant::DATETIME,
            'updated_at' => DataCastConstant::DATETIME,
            'age'        => DataCastConstant::NUMBER
        ];

    protected array $params = [];

    public array $relations = [];

    public function __construct()
    {
        $this->params           = request()->all();
        $this->operatorPatterns = array_keys($this->operators);
    }

    /**
     * Add more conditions
     *
     * @param array $param
     *
     * @return QueryHelper
     */
    public function addParams(array $param): static
    {
        $this->params = array_merge($this->params, $param);

        return $this;
    }

    /**
     * Remove one condition
     *
     * @param string $param
     *
     * @return QueryHelper
     */
    public function removeParam(string $param): static
    {
        if (key_exists($param, $this->params))
            unset($this->params[$param]);

        return $this;
    }

    /**
     * Add more cast params
     *
     * @param array $castParams
     *
     * @return QueryHelper
     */
    public function addCastParams(array $castParams): static
    {
        $this->params = array_merge($this->castParams, $castParams);

        return $this;
    }

    /**
     * Remove cast param
     *
     * @param string $castParam
     *
     * @return QueryHelper
     */
    public function removeCastParam(string $castParam): static
    {
        if (key_exists($castParam, $this->castParams))
            unset($this->params[$castParam]);

        return $this;
    }

    /**
     * Get all conditions from Request
     * @return array
     */
    public function getConditions(): array
    {
        // Remove all params of pagination
        foreach ($this->excludedOperators as $operator) {
            $this->removeParam($operator);
        }

        $conditions = [];
        foreach ($this->params as $paramKey => $paramValue) {
            if ($paramValue === '' || $paramValue === null) continue;

            // Basic query with equal clause
            if (!Str::endsWith($paramKey, $this->operatorPatterns)) {
                $conditions[] = [
                    'column'   => $paramKey,
                    'operator' => OperatorConstant::EQUAL,
                    'value'    => $this->_castParamValue($paramKey, $paramValue)
                ];
                continue;
            }

            foreach ($this->operators as $keyOperator => $operator) {
                if (!Str::endsWith($paramKey, $keyOperator))
                    continue;

                /**
                 * Get column from $paramKey
                 * $paramKey will be format with: {table}__{column}{operatorPatent}. Such as: user__age__lt OR age__lt OR age
                 */
                $column = Str::replaceLast($keyOperator, '', $paramKey);

                /**
                 * Format column name to query
                 * Column fre convert will be format with: {table}__{column}. Such as user__age OR age
                 */
                $tmp = explode('__', $column);

                if (count($tmp) == 1)
                    $column = $tmp[0];
                else
                    $column = "$tmp[0].$tmp[1]";

                // If $paramKey match $keyOperator
                $value        = $this->_castParamValue($column, $paramValue);
                $conditions[] = [
                    'column'   => $column,
                    'operator' => $operator,
                    'value'    => $operator === 'like' ? "%$value%" : $value
                ];

            }
        }

        return $conditions;
    }

    /**
     * Cast data to specific DataType
     *
     * @param $column
     * @param $value
     *
     * @return float|int|Carbon|string
     */
    private function _castParamValue($column, $value): float|int|Carbon|string
    {
        if (!key_exists($column, $this->castParams))
            return $value;

        $dataType = $this->castParams[$column];

        return match ($dataType) {
            DataCastConstant::DATE => Carbon::createFromDate($value),
            DataCastConstant::DATETIME => Carbon::parse($value),
            DataCastConstant::NUMBER, DataCastConstant::DOUBLE => (double)$value,
            DataCastConstant::INT => (int)$value,
            default => (string)$value
        };
    }

    /**
     * Get limit records
     *
     * @return int
     */
    public static function limit(): int
    {
        return request('limit') ?? config('laravel-base.limit') ?? 10;
    }

    /**
     * Get Order by column and type
     *
     * @return array|null
     */
    public function getOrderBy(): ?array
    {
        $query = request()->input('order_by');
        if (!$query)
            return null;

        $sort = preg_split("/[\s]+/", trim($query));

        return [
            'column' => $sort[0],
            'type'   => $sort[1] ?? 'ASC'
        ];
    }

    /**
     * Add conditions and order by
     *
     * @param Model  $model
     *
     * @param String $alias
     *
     * @return Builder
     */
    public function buildQuery(Model $model, string $alias = ''): Builder
    {
        $tableName = $model->getTable();
        if ($alias)
            $model = $model->from($tableName, $alias);

        if ($this->relations)
            $model = $model->with($this->relations);

        // Add where condition
        foreach ($this->getConditions() as $cond) {
            // If condition empty
            if (!$cond)
                continue;
            // If don't exist table or column
            if (!Schema::hasTable($tableName))
                continue;
            $model = $model->where($cond['column'], $cond['operator'], $cond['value']);
        }

        // Sort data
        $order = $this->getOrderBy();

        if ($order) {
            $model = $model->orderBy($order['column'], $order['type']);
        } else {
            if ($alias)
                $model = $model->orderBy("$alias.id", 'DESC');
            else
                $model = $model->orderBy('id', 'DESC');
        }

        return $model;
    }

    /**
     * Append excluded operators
     *
     * @param array $operators
     *
     * @return QueryHelper
     */
    public function addExcludedOperators(array $operators): static
    {
        array_push($this->excludedOperators, ...$operators);

        return $this;
    }

    /**
     * Add relations for query
     *
     * @param string|array $relations
     */
    public function with(string|array $relations)
    {
        $this->relations = (array)$relations;
    }
}
