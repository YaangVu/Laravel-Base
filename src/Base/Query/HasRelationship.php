<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use Illuminate\Database\Eloquent\Builder;
use YaangVu\LaravelBase\Base\DataObject\Relation;

trait HasRelationship
{
    private null|string|array $with       = null;
    private null|string|array $withCount  = null;
    private null|string|array $withExists = null;
    private ?Relation         $withSum    = null;
    private ?Relation         $withMin    = null;
    private ?Relation         $withMax    = null;
    private ?Relation         $withAvg    = null;

    /**
     *  Set the relationships that should be eager loaded.
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function relate(Builder $builder): Builder
    {
        if ($this->getWith())
            $builder->with($this->getWith());

        if ($this->getWithCount())
            $builder->withCount($this->getWithCount());

        if ($this->getWithExists())
            $builder->withCount($this->getWithExists());

        if ($this->getWithSum())
            $builder->withSum($this->getWithSum()->getRelation(), $this->getWithSum()->getColumn());

        if ($this->getWithMin())
            $builder->withMin($this->getWithMin()->getRelation(), $this->getWithMin()->getColumn());

        if ($this->getWithMax())
            $builder->withMax($this->getWithMax()->getRelation(), $this->getWithMax()->getColumn());

        if ($this->getWithAvg())
            $builder->withAvg($this->getWithAvg()->getRelation(), $this->getWithAvg()->getColumn());

        return $builder;
    }

    /**
     * @return array|string|null
     */
    public function getWith(): array|string|null
    {
        return $this->with;
    }

    /**
     * @param array|string|null $with
     *
     * @return $this
     */
    public function setWith(array|string|null $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getWithCount(): array|string|null
    {
        return $this->withCount;
    }

    /**
     * @param array|string|null $withCount
     *
     * @return $this
     */
    public function setWithCount(array|string|null $withCount): static
    {
        $this->withCount = $withCount;

        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getWithExists(): array|string|null
    {
        return $this->withExists;
    }

    /**
     * @param array|string|null $withExists
     *
     * @return $this
     */
    public function setWithExists(array|string|null $withExists): static
    {
        $this->withExists = $withExists;

        return $this;
    }

    /**
     * @return Relation|null
     */
    public function getWithSum(): ?static
    {
        return $this->withSum;
    }

    /**
     * @param Relation|null $withSum
     *
     * @return $this
     */
    public function setWithSum(?Relation $withSum): static
    {
        $this->withSum = $withSum;

        return $this;
    }

    /**
     * @return Relation|null
     */
    public function getWithMin(): ?Relation
    {
        return $this->withMin;
    }

    /**
     * @param Relation|null $withMin
     *
     * @return $this
     */
    public function setWithMin(?Relation $withMin): static
    {
        $this->withMin = $withMin;

        return $this;
    }

    /**
     * @return Relation|null
     */
    public function getWithMax(): ?Relation
    {
        return $this->withMax;
    }

    /**
     * @param Relation|null $withMax
     *
     * @return $this
     */
    public function setWithMax(?Relation $withMax): static
    {
        $this->withMax = $withMax;

        return $this;
    }

    /**
     * @return Relation|null
     */
    public function getWithAvg(): ?Relation
    {
        return $this->withAvg;
    }

    /**
     * @param Relation|null $withAvg
     *
     * @return $this
     */
    public function setWithAvg(?Relation $withAvg): static
    {
        $this->withAvg = $withAvg;

        return $this;
    }
}
