<?php
/**
 * @Author yaangvu
 * @Date   Feb 05, 2023
 */

namespace YaangVu\LaravelBase\Base\Utility\Query;

use YaangVu\LaravelBase\Base\DataObject\Sort;
use YaangVu\LaravelBase\Exception\BadRequestException;

trait Sortable
{
    /**
     * @var Sort[]
     */
    private array $sorts;

    /**
     * @return Sort[]
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @param Sort[] $sorts
     *
     * @return static
     */
    public function setSorts(array $sorts): static
    {
        $this->sorts = $sorts;

        return $this;
    }

    /**
     * Parse and add more Sort clause from request
     *
     * @Author yaangvu
     * @Date   Feb 05, 2023
     *
     * @param string|string[] $sorts
     *
     * @return static
     */
    public function parseSorts(string|array $sorts): static
    {
        // reset sort
        $this->setSorts([]);

        if (is_array($sorts))
            $sortClauses = $sorts;
        else
            $sortClauses = preg_split("/,+/", $sorts);

        foreach ($sortClauses as $sortClause) {
            $this->parseSort($sortClause);
        }

        return $this;
    }

    /**
     * Parse sort string to Sort Object
     *
     * @Author      yaangvu
     * @Date        Feb 28, 2023
     *
     * @param string $sortString
     *
     * @return $this
     */
    public function parseSort(string $sortString): static
    {
        $sortArr = preg_split("/\s+/", trim($sortString));
        if (count($sortArr) > 2)
            throw new BadRequestException("Order by clause is invalid");

        $sort = new Sort();
        $sort->setColumn($sortArr[0]);
        $sort->setType($sortArr[1] ?? 'ASC');

        return $this->addSort($sort);
    }

    /**
     * Add 1 more Sort clause
     *
     * @Author      yaangvu
     * @Date        Feb 28, 2023
     *
     * @param Sort $sort
     *
     * @return $this
     */
    public function addSort(Sort $sort): static
    {
        $this->sorts[] = $sort;

        return $this;
    }
}