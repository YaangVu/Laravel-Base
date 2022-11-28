<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use YaangVu\LaravelBase\Base\Clauses\Condition;
use YaangVu\LaravelBase\Base\Enums\OperatorEnum;
use YaangVu\LaravelBase\Base\Enums\OperatorPatternEnum;
use YaangVu\LaravelBase\Base\Facades\LikeConditionMaker;
use YaangVu\LaravelBase\Base\Query\HasCondition;
use YaangVu\LaravelBase\Base\Query\Sortable;
use YaangVu\LaravelBase\Helpers\CanCast;

trait HasParameter
{
    use HasCondition, CanCast, Sortable;

    /**
     * Add more param to request
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param object $request
     * @param array  $data
     *
     * @return object
     */
    public function mergeParams(object $request, array $data = []): object
    {
        if ($request instanceof Request)
            return $request->merge($data);
        else if ($request instanceof Model) {
            foreach ($data as $key => $value)
                $request->setAttribute($key, $value);

            return $request;
        } else {
            foreach ($data as $key => $value)
                $request->{$key} = $value;

            return $request;
        }
    }

    /**
     * Handle value before add or update
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param mixed $value
     *
     * @return string|Carbon|int|float
     */
    public function handleRequestValue(mixed $value): string|Carbon|int|float
    {
        return $this->cast($value);
    }

    /**
     *  Parse all params from request
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param array $params
     *
     * @return $this
     */
    public function parseParams(array $params = []): static
    {
        $params = $params ?: request()->all();

        $this->parseConditions($params)
             ->parseOrderBy($params['order_by'] ?? null);

        if (isset($params['limit']))
            $this->setLimit((int)$params['limit']);

        return $this;
    }

    /**
     * Parse from Params to Conditions
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param array $params
     *
     * @return $this
     */
    public function parseConditions(array $params = []): static
    {
        foreach ($params as $key => $value)
            $this->parseCondition($key, $value);

        return $this;
    }

    /**
     *  Parse from Key & Value to Condition
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function parseCondition(string $key, mixed $value): static
    {
        // If $value is empty or null and can not be null then return
        if (($value === '' || $value === null) && $this->isNullableValue() === false)
            return $this;

        // If $key is one of $excludedParams then return
        if (in_array($key, $this->excludedParams))
            return $this;

        [$table, $column, $operator, $operatorPattern, $value] = $this->deserialize($key, $value);

        $condition = new Condition();
        $condition->setTable($table);
        $condition->setColumn(($table ? "$table." : '') . $column);
        $condition->setOperator($operator);
        $condition->setOperatorPattern($operatorPattern);
        $condition->setValue($value);

        return $this->addCondition($condition);
    }

    /**
     * Deserialize Key Parameter
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array [$table, $column, $operator, $value]
     */
    public function deserialize(string $key, mixed $value): array
    {
        switch (substr_count($key, $this->getSeparator())) {
            // If $query has formatted like: ${column}__${operator}
            case 1:
                [$table, $column, $operatorPatternValue] = [null, ...explode($this->getSeparator(), $key)];
                break;

            // If $query has formatted like: ${table}__${column}__${operator}
            case 2:
                [$table, $column, $operatorPatternValue] = explode($this->getSeparator(), $key);
                break;

            // If $query has formatted like: ${column}
            default:
                $table                = null;
                $column               = $key;
                $operatorPatternValue = OperatorPatternEnum::EQUAL->value;
                break;
        }

        $operatorPattern = OperatorPatternEnum::from($operatorPatternValue);
        $operatorName    = $operatorPattern->name;

        if ($operatorPattern === OperatorPatternEnum::LIKE) {
            $operator = LikeConditionMaker::make($this->driver)->like()->value;
            $value    = LikeConditionMaker::make($this->driver)->value($value);
        } else
            $operator = OperatorEnum::toArray()[$operatorName];

        return [$table, $column, $operator, $operatorPattern, $value];
    }

    /**
     * Convert request to array
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param object $request
     *
     * @return array
     */
    public static function toArray(object $request): array
    {
        if ($request instanceof Request || $request instanceof Model)
            return $request->toArray();
        else
            return (array)$request;
    }
}
