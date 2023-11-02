<?php

namespace YaangVu\LaravelBase\Base\Utility\Query;

use Illuminate\Database\Eloquent\Builder;

trait HasRelationship
{
    private string|array $with           = '';
    private string|array $withCount      = '';
    private string|array $withAvg        = '';
    private string|array $withSum        = '';
    private string|array $withMax        = '';
    private string|array $withMin        = '';
    private string       $relationColumn = '';

    /**
     * Get eager loading
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function relate(Builder $builder): Builder
    {
        if ($this->getWith())
            $builder = $builder->with($this->getWith());
        if ($this->getWithCount())
            $builder = $builder->withCount($this->getWithCount());
        if ($this->getWithAvg())
            $builder = $builder->withAvg($this->getWithAvg(), $this->relationColumn);
        if ($this->getWithSum())
            $builder = $builder->withSum($this->getWithSum(), $this->relationColumn);
        if ($this->getWithMin())
            $builder = $builder->withMin($this->getWithMin(), $this->relationColumn);
        if ($this->getWithMax())
            $builder = $builder->withMax($this->getWithMax(), $this->relationColumn);

        return $builder;
    }

    public function getWith(): array|string
    {
        return $this->with;
    }

    public function setWith(array|string $with): static
    {
        $this->with = $with;

        return $this;
    }

    public function getWithCount(): array|string
    {
        return $this->withCount;
    }

    public function setWithCount(array|string $withCount): static
    {
        $this->withCount = $withCount;

        return $this;
    }

    public function getWithAvg(): array|string
    {
        return $this->withAvg;
    }

    public function setWithAvg(array|string $withAvg): static
    {
        [$this->withAvg, $this->relationColumn] = explode('__', $withAvg);

        return $this;
    }

    public function getWithSum(): array|string
    {
        return $this->withSum;
    }

    public function setWithSum(array|string $withSum): static
    {
        [$this->withSum, $this->relationColumn] = explode('__', $withSum);

        return $this;
    }

    public function getWithMin(): array|string
    {
        return $this->withMin;
    }

    public function setWithMin(array|string $withMin): static
    {
        [$this->withMin, $this->relationColumn] = explode('__', $withMin);

        return $this;
    }

    public function getWithMax(): array|string
    {
        return $this->withMax;
    }

    public function setWithMax(array|string $withMax): static
    {
        [$this->withMax, $this->relationColumn] = explode('__', $withMax);

        return $this;
    }

    /**
     * Add more $with relationship
     *
     * @param string|array $with
     *
     * @return $this
     */
    public function addWith(string|array $with): static
    {
        return $this->setWith(array_merge($this->convertToArray($this->getWith()), $this->convertToArray($with)));
    }

    /**
     * Convert data to array
     *
     * @param string|array $data
     *
     * @return string[]
     */
    private function convertToArray(string|array $data): array
    {
        return $data ? (is_array($data) ? $data : [$data]) : [];
    }

    /**
     * Add more $withCount relationship
     *
     * @param string|array $withCount
     *
     * @return $this
     */
    public function addWithCount(string|array $withCount): static
    {
        return $this->setWithCount(
            array_merge($this->convertToArray($this->getWithCount()), $this->convertToArray($withCount))
        );
    }

    /**
     * Add more $withSum relationship
     *
     * @param string|array $withAvg
     *
     * @return $this
     */
    public function addWithAvg(string|array $withAvg): static
    {
        return $this->setWithAvg(
            array_merge($this->convertToArray($this->getWithAvg()), $this->convertToArray($withAvg))
        );
    }

    /**
     * Add more $with relationship
     *
     * @param string|array $withSum
     *
     * @return $this
     */
    public function addWithSum(string|array $withSum): static
    {
        return $this->setWithSum(
            array_merge($this->convertToArray($this->getWithSum()), $this->convertToArray($withSum))
        );
    }

    /**
     * Add more $withMax relationship
     *
     * @param string|array $withMax
     *
     * @return $this
     */
    public function addWithMax(string|array $withMax): static
    {
        return $this->setWithMax(
            array_merge($this->convertToArray($this->getWithMax()), $this->convertToArray($withMax))
        );
    }

    /**
     * Add more $withMin relationship
     *
     * @param string|array $withMin
     *
     * @return $this
     */
    public function addWithMin(string|array $withMin): static
    {
        return $this->setWithMin(
            array_merge($this->convertToArray($this->getWithMin()), $this->convertToArray($withMin))
        );
    }
}