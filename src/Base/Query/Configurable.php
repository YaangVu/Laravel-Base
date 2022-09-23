<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait Configurable
{
    /**
     * The model will be use for all functions
     *
     * @var Model
     */
    public Model $model;
    /**
     * Limitation of pagination
     *
     * @var int
     */
    private int $limit;
    /**
     * The parameters were excluded
     *
     * @var array
     */
    private array $excludedParams;
    /**
     * @var string Separator
     */
    private string $separator;
    /**
     * The value of parameter can be null or not
     *
     * @var bool $nullableValue
     */
    private bool $nullableValue;
    /**
     * Query builder
     *
     * @var Builder
     */
    private Builder $builder;

    /**
     * The fill able attributes
     *
     * @var string[]
     */
    private array $fillAbles;

    /**
     * The guarded attributes
     *
     * @var string[]
     */
    private array $guarded;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    private string $primaryKey;

    /**
     *  The table associated with the model.
     *
     * @var string
     */
    private string $table;

    /**
     * The PDO driver name.
     *
     * @var string
     */
    private string $driver;

    /**
     * Cache time to live in seconds
     *
     * @var int
     */
    private int $ttl;

    /**
     *  Initial default configuration
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->setLimit(config('laravel-base.query.limit'))
                    ->setSeparator(config('laravel-base.query.separator'))
                    ->setNullableValue(config('laravel-base.query.nullable_value'))
                    ->setExcludedParams(['limit', 'order_by', 'page']);
    }

    /**
     * Initial default Service attributes
     *
     * @Author yaangvu
     * @Date   Sep 02, 2022
     *
     */
    public function initAttrs(): void
    {
        // Get attributes from model
        $this->setFillAbles($this->model->getFillable())
             ->setGuarded($this->model->getGuarded())
             ->setPrimaryKey($this->model->getKeyName())
             ->setTable($this->model->getTable())
             ->setDriver($this->model->getConnection()->getDriverName())
             ->setBuilder($this->model->query())
             ->setTtl(config('laravel-base.cache.ttl')) // 1 day
        ;
    }

    /**
     * @return array
     */
    public function getGuarded(): array
    {
        return $this->guarded;
    }

    /**
     * @param array $guarded
     *
     * @return static
     */
    public function setGuarded(array $guarded): static
    {
        $this->guarded = $guarded;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return static
     */
    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return static
     */
    public function setLimit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return array
     */
    public function getExcludedParams(): array
    {
        return $this->excludedParams;
    }

    /**
     * @param array $excludedParams
     *
     * @return static
     */
    public function setExcludedParams(array $excludedParams): static
    {
        $this->excludedParams = $excludedParams;

        return $this;
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
     * @return static
     */
    public function setSeparator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullableValue(): bool
    {
        return $this->nullableValue;
    }

    /**
     * @param bool $nullableValue
     *
     * @return static
     */
    public function setNullableValue(bool $nullableValue): static
    {
        $this->nullableValue = $nullableValue;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @return static
     */
    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param Builder $builder
     *
     * @return static
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return array
     */
    public function getFillAbles(): array
    {
        return $this->fillAbles;
    }

    /**
     * @param array $fillAbles
     *
     * @return static
     */
    public function setFillAbles(array $fillAbles): static
    {
        $this->fillAbles = $fillAbles;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @param string $primaryKey
     *
     * @return static
     */
    public function setPrimaryKey(string $primaryKey): static
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     *
     * @return static
     */
    public function setDriver(string $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @return static
     */
    public function setTtl(int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }
}
