<?php
/**
 * @Author yaangvu
 * @Date   Feb 04, 2023
 */

namespace YaangVu\LaravelBase\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use YaangVu\LaravelBase\Base\DataObject\Sort;
use YaangVu\LaravelBase\Base\Enum\ClauseEnum;
use YaangVu\LaravelBase\Base\Utility\Query\HasCondition;
use YaangVu\LaravelBase\Base\Utility\Query\HasKeywordSearch;
use YaangVu\LaravelBase\Base\Utility\Query\HasRelationship;
use YaangVu\LaravelBase\Base\Utility\Query\HasSelection;
use YaangVu\LaravelBase\Base\Utility\Query\Pageable;
use YaangVu\LaravelBase\Base\Utility\Query\Sortable;

class ParamHandler
{
    use HasCondition, Sortable, HasSelection, Pageable, HasKeywordSearch, HasRelationship, Macroable;

    /**
     * List Keys of parameter will be excluded before a query into a database
     *
     * @var string[]
     */
    private array $excludedKeys;

    public function __construct()
    {
        $this->setSeparator(config('laravel-base.query.separator'))
            ->setNullableValue(config('laravel-base.query.nullable_value'))
            ->setLimit(config('laravel-base.query.limit'))
            ->setExcludedKeys(
                [
                    'limit',
                    'sort',
                    'page',
                    'select',
                    'keyword',
                    'with',
                    'with_count',
                    'with_sum',
                    'with_avg',
                    'with_min',
                    'with_max'
                ]
            )->setDefaultSort();
    }

    /**
     * Set default Sort before a parse query from parameters
     *
     * @Author yaangvu
     * @Date   Feb 16, 2023
     *
     * @param string $column
     *
     * @return $this
     */
    public function setDefaultSort(string $column = 'id'): static
    {
        $sort = new Sort();
        $sort->setColumn($column);
        $sort->setType('DESC');
        $this->setSorts([$sort]);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedKeys(): array
    {
        return $this->excludedKeys;
    }

    /**
     * @param string[] $excludedKeys
     *
     * @return static
     */
    public function setExcludedKeys(array $excludedKeys): static
    {
        $this->excludedKeys = $excludedKeys;

        return $this;
    }

    /**
     * Add more exclude key
     *
     * @Author yaangvu
     * @Date   Feb 16, 2023
     *
     * @param string|string[] $keys
     *
     * @return $this
     */
    public function exclude(string ...$keys): static
    {
        foreach ($keys as $key)
            $this->excludedKeys[] = $key;

        return $this;
    }

    /**
     * Parse All Request Parameters to Param Clause
     *
     * @Author yaangvu
     * @Date   Feb 05, 2023
     *
     * @param array $params
     *
     * @return $this
     */
    public function parseParams(array $params = []): static
    {
        $params = $params ?: request()->all();

        foreach ($params as $key => $value) {
            $this->parseParam($key, $value);
        }

        return $this;
    }

    /**
     * Parse param to query clause
     *
     * @Author yaangvu
     * @Date   Feb 05, 2023
     *
     * @param string $key
     * @param mixed $value
     *
     * @return static
     */
    public function parseParam(string $key, mixed $value): static
    {
        return match (Str::lower($key)) {
            ClauseEnum::LIMIT->value => $this->setLimit($value),
            ClauseEnum::PAGE->value => $this->setPage($value),
            ClauseEnum::SELECT->value => $this->parseSelections($value),
            ClauseEnum::SORT->value => $this->parseSorts($value),
            ClauseEnum::KEYWORD->value => $this->setKeyword($value),
            ClauseEnum::WITH->value => $this->addWith($value),
            ClauseEnum::WITH_COUNT->value => $this->addWithCount($value),
            ClauseEnum::WITH_AVG->value => $this->addWithAvg($value),
            ClauseEnum::WITH_SUM->value => $this->addWithSum($value),
            ClauseEnum::WITH_MAX->value => $this->addWithMax($value),
            ClauseEnum::WITH_MIN->value => $this->addWithMin($value),
            default => $this->parseCondition($key, $value)
        };
    }

}
