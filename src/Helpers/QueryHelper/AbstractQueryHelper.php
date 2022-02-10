<?php

namespace YaangVu\LaravelBase\Helpers\QueryHelper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use YaangVu\LaravelBase\Clauses\Condition;
use YaangVu\LaravelBase\Clauses\OrderBy;
use YaangVu\LaravelBase\Constants\DataTypeConstant;
use YaangVu\LaravelBase\Constants\OperatorConstant;
use YaangVu\LaravelBase\Helpers\DataHelper\DataTypeHelper;

abstract class AbstractQueryHelper implements QueryHelper
{
    private string $delimiter;

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

    /**
     * Relationships of model
     * @var array
     */
    public array $relations = [];

    /**
     * @var DataTypeHelper
     */
    public DataTypeHelper $dataTypeHelper;

    public function __construct()
    {
        $this->dataTypeHelper = new DataTypeHelper();
        $this->setOperators($this->operators)
             ->setOperatorPatterns($this->operatorPatterns)
             ->setParams($this->params)
             ->setExcludedOperators($this->excludedOperators)
             ->_setCastParams($this->castParams)
             ->_setDelimiter(config('laravel-base.query.delimiter'));
    }

    /**
     * @param string $delimiter
     *
     * @return AbstractQueryHelper
     */
    private function _setDelimiter(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
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
    private function _setCastParams(array $params = []): static
    {
        if (!$params)
            $params = [
                'date'       => DataTypeConstant::DATE,
                'created_at' => DataTypeConstant::DATETIME,
                'updated_at' => DataTypeConstant::DATETIME,
                'age'        => DataTypeConstant::INT
            ];
        foreach ($params as $param => $type)
            $this->addCastParam($param, $type);

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
     * @return AbstractQueryHelper
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
     * @return AbstractQueryHelper
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
     * @param string $param
     * @param mixed  $value
     *
     * @return AbstractQueryHelper
     */
    public function addParam(string $param, mixed $value): static
    {
        $this->params = array_merge($this->params, [$param => $value]);

        return $this;
    }

    /**
     * Remove one condition
     *
     * @param string $param
     *
     * @return AbstractQueryHelper
     */
    public function removeParam(string $param): static
    {
        if (key_exists($param, $this->params))
            unset($this->params[$param]);

        return $this;
    }

    /**
     * Add one more cast param
     *
     * @param string $param
     * @param string $type
     *
     * @return AbstractQueryHelper
     */
    public function addCastParam(string $param, string $type): static
    {
        $this->dataTypeHelper->addParam($param, $type);

        return $this;
    }

    /**
     * Remove cast param
     *
     * @param string $param
     *
     * @return AbstractQueryHelper
     */
    public function removeCastParam(string $param): static
    {
        $this->dataTypeHelper->removeParam($param);

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
     * @return Condition[]
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
     * @return Condition|null
     */
    private function _getCondition(string $paramKey, mixed $paramValue): ?Condition
    {
        // Ignore if $value is empty or null
        if ($paramValue === '' || $paramValue === null)
            return null;

        // initial $table name
        $table = null;

        $countDelimiter = substr_count($paramKey, $this->delimiter);
        switch ($countDelimiter) {
            // If $query has formatted like: ${column}__${operator}
            case 1:
                [$column, $operatorPattern] = explode($this->delimiter, $paramKey);
                break;

            // If $query has formatted like: ${table}__${column}__${operator}
            case 2:
                [$table, $column, $operatorPattern] = explode($this->delimiter, $paramKey);
                break;

            // If $query has formatted like: ${column}
            default:
                $column          = $paramKey;
                $operatorPattern = OperatorConstant::EQUAL_PATTERN;
                break;
        }

        $condition = new Condition();
        $condition->setTable($table);
        $condition->setColumn(($table ? "$table." : '') . $column);
        $condition->setOperator($this->operators[$operatorPattern] ?? OperatorConstant::EQUAL);
        if ($operatorPattern === OperatorConstant::LIKE_PATTERN)
            $value = "%$paramValue%";
        else
            $value = $this->dataTypeHelper->cast($paramValue, $this->dataTypeHelper->getType($column));
        $condition->setValue($value);

        return $condition;
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
     * @return OrderBy|null
     */
    public function getOrderBy(): ?OrderBy
    {
        $query = request()->input('order_by');
        if (!$query)
            return null;

        $sort = preg_split("/[\s]+/", trim($query));

        $orderBy = new OrderBy();
        $orderBy->setColumn($sort[0]);
        $orderBy->setType($sort[1] ?? 'ASC');

        return $orderBy;
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
            $model = $model->where($cond->getColumn(), $cond->getOperator(), $cond->getValue());
        }

        // Sort data
        if ($order = $this->getOrderBy()) {
            $model = $model->orderBy($order->getColumn(), $order->getType());
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
        array_push($this->relations, ...$relations);

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
