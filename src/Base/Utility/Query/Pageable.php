<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Utility\Query;

trait Pageable
{
    private int $limit;
    private int $page;

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
     * @return Pageable
     */
    public function setLimit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return static
     */
    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }
}