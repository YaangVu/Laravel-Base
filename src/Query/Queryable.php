<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Query;

use Illuminate\Database\Eloquent\Builder;
use YaangVu\LaravelBase\Traits\HasParameter;

trait Queryable
{
    use HasRelationship, HasParameter, Configurable;

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
    public function buildQuery(Builder $builder, ?string $alias = null, array $params = []): Builder
    {
        $this->configure()->parseParams($params);

        $builder = $this->relate($builder);

        if ($alias)
            $builder = $builder->from($this->getTable(), $alias);

        // Add where condition
        foreach ($this->getConditions() as $cond) {
            $builder = $builder->where($cond->getColumn(), $cond->getOperator(), $cond->getValue());
        }

        // Sort data
        if ($orderBy = $this->getOrderBy()) {
            $builder = $builder->orderBy($orderBy->getColumn(), $orderBy->getType());
        } else {
            $builder = $builder->orderBy(($alias ? "$alias." : "") . $this->getPrimaryKey(), 'DESC');
        }

        return $builder;
    }
}
