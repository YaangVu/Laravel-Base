<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Query;

use YaangVu\Exceptions\BadRequestException;
use YaangVu\LaravelBase\Clauses\OrderBy;

trait Sortable
{
    private ?OrderBy $orderBy = null;

    public function parseOrderBy(?string $orderBy = null): static
    {
        $clause = $orderBy ?: request()->input('order_by');
        if (!$clause)
            return $this;

        $orderArr = preg_split("/\s+/", trim($clause));
        if (count($orderArr) > 2)
            throw new BadRequestException("Order by clause is invalid");

        $order = new OrderBy();
        $order->setColumn($orderArr[0]);
        $order->setType($orderArr[1] ?? 'ASC');

        return $this->setOrderBy($order);
    }

    /**
     * @return OrderBy|null
     */
    public function getOrderBy(): ?OrderBy
    {
        return $this->orderBy;
    }

    /**
     * @param OrderBy|null $orderBy
     *
     * @return Sortable
     */
    public function setOrderBy(?OrderBy $orderBy): static
    {
        $this->orderBy = $orderBy;

        return $this;
    }
}
