<?php

namespace YaangVu\LaravelBase\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use YaangVu\LaravelBase\Constants\DataCastConstant;
use YaangVu\LaravelBase\Constants\OperatorConstant;

class QueryHelper
{
    protected string $separator = '__';

    protected array $operatorPatterns = [];

    /**
     * Operators to query into DB
     * @var array
     */
    protected array $operators = [];

    /**
     * Operators were excluded
     * @var array|string[]
     */
    protected array $excludedOperators = [];

    /**
     * Params will be cast to data type
     * @var array
     */
    protected array $castParams = [];

    /**
     * Parameters for query in database
     * @var array
     */
    protected array $params = [];

    public array $relations = [];

    public function __construct()
    {
        $this->setOperators($this->operators)
             ->setOperatorPatterns($this->operatorPatterns)
             ->setParams($this->params)
             ->setCastParams($this->castParams)
             ->setExcludedOperators($this->excludedOperators);
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     *
     * @return QueryHelper
     */
    public function setSeparator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Get list Operators
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getOperators(): array
    {
        return $this->operators;
    }

    /**
     * Set Operators
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operators
     *
     * @return QueryHelper
     */
    public function setOperators(array $operators = []): static
    {
        $this->operators = $operators ?: OperatorConstant::DEFAULT_OPERATORS;

        return $this;
    }

    /**
     * Set Operator patterns
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operatorPatterns
     *
     * @return QueryHelper
     */
    public function setOperatorPatterns(array $operatorPatterns = []): static
    {
        $this->operatorPatterns = $operatorPatterns ?: array_keys($this->operators);

        return $this;
    }

    /**
     * Get Parameters
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set params for query
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params = []): static
    {
        $this->params = $params ?: request()->all();

        return $this;
    }

    /**
     * Add one more condition
     *
     * @param array $param
     *
     * @return QueryHelper
     */
    public function addParam(array $param): static
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
     * Get Cast parameters
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getCastParams(): array
    {
        return $this->castParams;
    }

    /**
     * Set cast params
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $params
     *
     * @return $this
     */
    public function setCastParams(array $params = []): static
    {
        $this->castParams = $params ?: [
            'date'       => DataCastConstant::DATE,
            'created_at' => DataCastConstant::DATETIME,
            'updated_at' => DataCastConstant::DATETIME,
            'age'        => DataCastConstant::NUMBER
        ];

        return $this;
    }

    /**
     * Add one more cast param
     *
     * @param array $castParam
     *
     * @return QueryHelper
     */
    public function addCastParam(array $castParam): static
    {
        $this->params = array_merge($this->castParams, $castParam);

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
     * Get Excluded Operators
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return string[]
     */
    public function getExcludedOperators(): array
    {
        return $this->excludedOperators;
    }

    /**
     * Set cast params
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operators
     *
     * @return $this
     */
    public function setExcludedOperators(array $operators = []): static
    {
        $this->excludedOperators = $operators ?: [
            'limit',
            'page',
            'order_by'
        ];

        return $this;
    }

    /**
     * Add more exclude operators
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param ...$operator
     *
     * @return $this
     */
    public function addExcludedOperators(...$operator): static
    {
        array_push($this->excludedOperators, ...$operator);

        return $this;
    }

    /**
     * Remove exclude operator
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param string $operator
     *
     * @return $this
     */
    public function removeExcludedOperators(string $operator): static
    {
        if (($key = array_search($operator, $this->excludedOperators)) !== false) {
            unset($this->excludedOperators[$key]);
        }

        return $this;
    }

    /**
     * Get all conditions from Request
     *
     * @param array $params
     *
     * @return array
     */
    public function getConditions(array $params = []): array
    {
        // Remove all params were excluded
        foreach ($this->excludedOperators as $excludedOperator) {
            $this->removeParam($excludedOperator);
        }

        $params     = $params ?: $this->params;
        $conditions = [];

        foreach ($params as $key => $value) {
            $condition = $this->_getCondition($key, $value);
            if ($condition)
                $conditions[] = $condition;
        }

        return $conditions;
    }

    /**
     * Get where query condition
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param string $paramKey with format will be: [${table}__]${column}__${operator}
     * @param mixed  $paramValue
     *
     * @return array
     */
    private function _getCondition(string $paramKey, mixed $paramValue): array
    {
        // Ignore if $value is empty or null
        if ($paramValue === '' || $paramValue === null)
            return [];

        // initial $table name
        $table = null;

        $countSeparator = substr_count($paramKey, $this->separator);
        switch ($countSeparator) {
            // If $query has formatted like: ${column}__${operator}
            case 1:
                [$column, $operatorPattern] = explode($this->separator, $paramKey);
                break;

            // If $query has formatted like: ${table}__${column}__${operator}
            case 2:
                [$table, $column, $operatorPattern] = explode($this->separator, $paramKey);
                break;

            // If $query has formatted like: ${column}
            default:
                $column          = $paramKey;
                $operatorPattern = OperatorConstant::EQUAL_PATTERN;
                break;
        }

        return [
            'table'           => $table,
            'column'          => ($table ? "$table." : '') . $column,
            'value'           => $this->_castParamValue($column, $paramValue, $operatorPattern),
            'operatorPattern' => $operatorPattern,
            'operator'        => $this->operators[$operatorPattern] ?? OperatorConstant::EQUAL,
        ];
    }

    /**
     * Cast data to specific DataType
     *
     * @param string $column
     * @param mixed  $value
     * @param string $pattern
     *
     * @return mixed
     */
    private function _castParamValue(string $column, mixed $value, string $pattern = ''): mixed
    {
        if ($pattern === OperatorConstant::LIKE_PATTERN)
            $value = "%$value%";

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
     * @param Model|Builder $model
     *
     * @param String        $alias
     *
     * @return Builder|Model
     */
    public function buildQuery(Model|Builder $model, string $alias = ''): Builder|Model
    {
        $tableName  = $model->getTable();
        $primaryKey = $model->getKeyName();

        if ($alias)
            $model = $model->from($tableName, $alias);

        if ($this->relations)
            $model = $model->with($this->relations);

        // Add where condition
        foreach ($this->getConditions() as $cond) {
            // If condition empty
            if (!$cond)
                continue;
            $model = $model->where($cond['column'], $cond['operator'], $cond['value']);
        }

        // Sort data
        if ($order = $this->getOrderBy()) {
            $model = $model->orderBy($order['column'], $order['type']);
        } else {
            $model = $model->orderBy(($alias ? "$alias." : "") . $primaryKey, 'DESC');
        }

        return $model;
    }

    /**
     * Add relations for query
     *
     * @param string|array $relations
     */
    public function with(...$relations): static
    {
        array_push($this->relations, $relations);

        return $this;
    }

    /**
     * Get relationships
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
