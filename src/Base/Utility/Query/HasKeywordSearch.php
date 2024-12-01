<?php

namespace YaangVu\LaravelBase\Base\Utility\Query;

trait HasKeywordSearch
{
    /**
     * @var string[]
     */
    private array $searchKeys = [];
    private mixed $keyword    = null;

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