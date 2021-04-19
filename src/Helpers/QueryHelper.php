<?php

namespace YaangVu\LaravelBase\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class QueryHelper
{
    private array $operatorPatterns;

    private array $operators
        = [
            '__gt' => '>', // Greater than
            '__ge' => '>=', // Greater than or equal
            '__lt' => '<', // Less than
            '__le' => '<=', // Less than or equal
            '__~'  => 'like' // Like
        ];

    private array $excludedOperators
        = [
            'limit',
            'page',
            'order_by'
        ];

    public array $params = [];

    protected ?string $relations = '';

    public function __construct()
    {
        $this->params           = request()->all();
        $this->operatorPatterns = array_keys($this->operators);
    }

    /**
     * Add more conditions
     *
     * @param array $param
     */
    public function addParam(array $param)
    {
        $this->params = array_merge($this->params, $param);
    }

    /**
     * Remove one condition
     *
     * @param string $param
     */
    public function removeParam(string $param)
    {
        if (key_exists($param, $this->params))
            unset($this->params[$param]);
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
        foreach ($this->params as $keyParam => $valueParam) {
            if ($valueParam === '' || $valueParam === null) continue;

            // Basic query with equal clause
            if (!Str::endsWith($keyParam, $this->operatorPatterns)) {
                $conditions[] = [
                    'column'   => $keyParam,
                    'operator' => '=',
                    'value'    => $valueParam
                ];
                continue;
            }

            foreach ($this->operators as $keyOperator => $operator) {
                $clause = explode($keyOperator, $keyParam);
                // If $keyParam not match $keyOperator then continue
                if (count($clause) == 1)
                    continue;

                $tmp = explode('__', $clause[0]);
                if (count($tmp) == 1)
                    $column = $clause[0];
                else
                    $column = $tmp[0] . '.' . $tmp[1];

                // If $keyParam match $keyOperator
                $conditions[] = [
                    'column'   => $column,
                    'operator' => $operator,
                    'value'    => $operator === 'like' ? "%$valueParam%" : $valueParam
                ];

            }
        }

        return $conditions;
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
     * @return Builder|Model
     */
    public function buildQuery(Model $model, string $alias = ''): Builder
    {
        $tableName = Str::snake(class_basename($model)) . 's';
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
     * @return void
     */
    public function addExcludedOperators(array $operators): void
    {
        array_push($this->excludedOperators, ...$operators);
    }

    /**
     * Set Relations Entity
     *
     * @param string|array $relations
     */
    public function with($relations): void
    {
        $this->relations = $relations;
    }
}
