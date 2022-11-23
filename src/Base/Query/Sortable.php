<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use YaangVu\Exceptions\BadRequestException;
use YaangVu\LaravelBase\Base\Clauses\OrderBy;

trait Sortable
{

    /**
     * @var OrderBy[]
     */
    private array $orders = [];

    public function parseOrderBy(?string $orderBy = null): static
    {
        $param = $orderBy ?: request()->input('order_by');
        if (!$param)
            return $this;

        $orderClauses = preg_split("/,+/", trim($param));

        foreach ($orderClauses as $orderClause) {
            $orderArr = preg_split("/\s+/", trim($orderClause));
            if (count($orderArr) > 2)
                throw new BadRequestException("Order by clause is invalid");

            $order = new OrderBy();
            $order->setColumn($orderArr[0]);
            $order->setType($orderArr[1] ?? 'ASC');

            $this->addOrderBy($order);
        }

        return $this;
    }

    /**
     * @return OrderBy[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @param array $orders
     *
     * @return Sortable
     */
    public function setOrders(array $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Add more OrderBy Clause
     *
     * @Author yaangvu
     * @Date   Nov 10, 2022
     *
     * @param OrderBy $orderBy
     *
     * @return $this
     */
    public function addOrderBy(OrderBy $orderBy): static
    {
        return $this->setOrders([...$this->getOrders(), $orderBy]);
    }
}
