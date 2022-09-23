<?php
/**
 * @Author yaangvu
 * @Date   Jul 26, 2022
 */

namespace YaangVu\LaravelBase\Base\Query;

use Illuminate\Database\Eloquent\Builder;
use YaangVu\LaravelBase\Base\Traits\HasParameter;
use YaangVu\LaravelBase\Helpers\Singleton;

class Query
{
    use HasRelationship, HasParameter, Configurable, Singleton;

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
    public static function buildGetAllQuery(Builder $builder, ?string $alias = null, array $params = []): Builder
    {
        self::getInstance()->configure()->parseParams($params);

        $builder = self::getInstance()->relate($builder);

        if ($alias)
            $builder = $builder->from(self::getInstance()->getTable(), $alias);

        // Add where condition
        foreach (self::getInstance()->getConditions() as $cond) {
            $builder = $builder->where($cond->getColumn(), $cond->getOperator(), $cond->getValue());
        }

        // Sort data
        if ($orderBy = self::getInstance()->getOrderBy()) {
            $builder = $builder->orderBy($orderBy->getColumn(), $orderBy->getType());
        } else {
            $builder = $builder->orderBy(($alias ? "$alias." : "") . self::getInstance()->getPrimaryKey(), 'DESC');
        }

        return $builder;
    }
}
