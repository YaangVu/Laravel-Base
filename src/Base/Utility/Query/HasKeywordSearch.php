<?php

namespace YaangVu\LaravelBase\Base\Utility\Query;

use Illuminate\Database\Eloquent\Builder;

trait HasKeywordSearch
{
    /**
     * @var string[]
     */
    private array $searchKeys = [];
    private mixed $keyword    = null;

    public function addKeywordQuery(Builder $builder, string $operator = 'like'): Builder
    {
        if (!$this->getKeyword() || !$this->getSearchKeys()) return $builder;

        $keyword = $this->getKeyword();

        return $builder->where(function (Builder $builder) use ($keyword, $operator) {
            foreach ($this->getSearchKeys() as $key)
                $builder->orWhere($key, $operator, "%$keyword%");
        });
    }

    public function getKeyword(): mixed
    {
        return $this->keyword;
    }

    public function setKeyword(mixed $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getSearchKeys(): array
    {
        return $this->searchKeys;
    }

    public function setSearchKeys(array $searchKeys): static
    {
        $this->searchKeys = $searchKeys;

        return $this;
    }

}