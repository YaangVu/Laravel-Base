<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use Illuminate\Database\Eloquent\Builder;
use YaangVu\LaravelBase\Base\Traits\HasParameter;

trait Query
{
    use HasRelationship, HasParameter, Configurable, HasSelection;

    /**
     *  Build query from parameters
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param Builder     $builder
     * @param string|null $alias
     * @param array       $params
     *
     * @return Builder
     */
    public function buildGetAllQuery(Builder $builder, ?string $alias = null, array $params = []): Builder
    {
        $this->configureForGetAllQuery()
             ->parseSelections($params)
             ->parseParams($params);

        $builder = $this->relate($builder)->select();

        if ($alias)
            $builder = $builder->from($this->getTable(), $alias);

        // Add where condition
        foreach ($this->getConditions() as $cond) {
            $builder = $builder->where($cond->getColumn(), $cond->getOperator(), $cond->getValue());
        }

        // Sort data
        if ($orderBy = count($this->getOrders())) {
            foreach ($this->getOrders() as $orderBy)
                $builder = $builder->orderBy($orderBy->getColumn(), $orderBy->getType());
        } else {
            $builder = $builder->orderBy(($alias ? "$alias." : "") . $this->getPrimaryKey(), 'DESC');
        }

        return $builder;
    }
}
