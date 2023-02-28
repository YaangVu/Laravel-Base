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
     * @param string $sortString
     *
     * @return static
     */
    public function parseSort(string $sortString): static
    {
        // reset sort
        $this->setSorts([]);

        $sortClauses = preg_split("/,+/", $sortString);

        foreach ($sortClauses as $sortClause) {
            $sortArr = preg_split("/\s+/", trim($sortClause));
            if (count($sortArr) > 2)
                throw new BadRequestException("Order by clause is invalid");

            $sort = new Sort();
            $sort->setColumn($sortArr[0]);
            $sort->setType($sortArr[1] ?? 'ASC');

            $this->addSort($sort);
        }

        return $this;
    }

    /**
     * @Description Add 1 more Sort clause
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