<?php

namespace YaangVu\LaravelBase\Base\Utility\Query;

use Illuminate\Database\Eloquent\Builder;
use YaangVu\LaravelBase\Base\DataObject\Relation;

trait HasRelationship
{
    /**
     * @var Relation[]
     */
    private array $with = [];

    /**
     * @var Relation[]
     */
    private array $withCount = [];

    /**
     * @var Relation[]
     */
    private array $withAvg = [];

    /**
     * @var Relation[]
     */
    private array $withSum = [];

    /**
     * @var Relation[]
     */
    private array $withMax = [];

    /**
     * @var Relation[]
     */
    private array $withMin = [];

    /**
     * Get eager loading
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function relate(Builder $builder): Builder
    {
        if ($this->getWith()) {
            $relations = $this->getRelations($this->getWith());
            $builder   = $builder->with($relations['relations']);
        }

        if ($this->getWithCount()) {
            $relations = $this->getRelations($this->getWithCount());
            $builder   = $builder->withCount($relations['relations']);
        }

        if ($this->getWithAvg()) {
            $relations = $this->getRelations($this->getWithAvg());
            $builder   = $builder->withAvg($relations['relations'], $relations['column']);
        }

        if ($this->getWithSum()) {
            $relations = $this->getRelations($this->getWithSum());
            $builder   = $builder->withSum($relations['relations'], $relations['column']);
        }

        if ($this->getWithMin()) {
            $relations = $this->getRelations($this->getWithMin());
            $builder   = $builder->withMin($relations['relations'], $relations['column']);
        }

        if ($this->getWithMax()) {
            $relations = $this->getRelations($this->getWithMax());
            $builder   = $builder->withMax($relations['relations'], $relations['column']);
        }

        return $builder;
    }

    public function getWith(): array
    {
        return $this->with;
    }

    public function setWith(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * @param Relation[] $relations
     *
     * @return array{relations: array, column: string}
     */
    public function getRelations(array $relations): array
    {
        $handledRelations = [];
        $column           = 'id';
        foreach ($relations as $relation) {
            $handledRelations[] = $relation->getRelation();
            $column             = $relation->getColumn() ?? $column;
        }

        return [
            'relations' => $handledRelations,
            'column'    => $column
        ];
    }

    public function getWithCount(): array
    {
        return $this->withCount;
    }

    public function setWithCount(array $withCount): static
    {
        $this->withCount = $withCount;

        return $this;
    }

    public function getWithAvg(): array
    {
        return $this->withAvg;
    }

    public function setWithAvg(array $withAvg): static
    {
        $this->withAvg = $withAvg;

        return $this;
    }

    public function getWithSum(): array
    {
        return $this->withSum;
    }

    public function setWithSum(array $withSum): static
    {
        $this->withSum = $withSum;

        return $this;
    }

    public function getWithMin(): array
    {
        return $this->withMin;
    }

    public function setWithMin(array $withMin): static
    {
        $this->withMin = $withMin;

        return $this;
    }

    public function getWithMax(): array
    {
        return $this->withMax;
    }

    public function setWithMax(array $withMax): static
    {
        $this->withMax = $withMax;

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
        $addedRelations = $this->convertToRelations($with);

        return $this->setWith(array_merge($this->getWith(), $addedRelations));
    }

    /**
     * Convert relation from request params to an array of Relation object
     *
     * @param string|array $reqRelations
     *
     * @return Relation[]
     */
    public function convertToRelations(string|array $reqRelations): array
    {
        $reqRelations = $this->convertToArray($reqRelations);
        $relations    = [];
        foreach ($reqRelations as $reqRelation) {
            if (str_contains($this->getSeparator(), $reqRelation))
                [$relationString, $column] = explode('__', $reqRelation);
            else {
                $relationString = $reqRelation;
                $column         = null;
            }
            $relation = new Relation();
            $relation->setRelation($relationString);
            $relation->setColumn($column ?? null);
            $relations[] = $relation;
        }

        return $relations;
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
        $addedRelations = $this->convertToRelations($withCount);

        return $this->setWithCount(array_merge($this->getWithCount(), $addedRelations));
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
        $addedRelations = $this->convertToRelations($withAvg);

        return $this->setWithAvg(array_merge($this->getWithAvg(), $addedRelations));
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
        $addedRelations = $this->convertToRelations($withSum);

        return $this->setWithSum(array_merge($this->getWithSum(), $addedRelations));
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
        $addedRelations = $this->convertToRelations($withMax);

        return $this->setWithMax(array_merge($this->getWithMax(), $addedRelations));
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
        $addedRelations = $this->convertToRelations($withMin);

        return $this->setWithMin(array_merge($this->getWithMin(), $addedRelations));
    }
}