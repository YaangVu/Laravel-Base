<?php
/**
 * @Author yaangvu
 * @Date   Feb 04, 2023
 */

namespace YaangVu\LaravelBase\Base\Facade;

use Illuminate\Support\Facades\Facade;
use YaangVu\LaravelBase\Base\DataObject\Condition;
use YaangVu\LaravelBase\Base\DataObject\Sort;
use YaangVu\LaravelBase\Base\ParamHandler;
use YaangVu\LaravelBase\Base\Utility\Query\HasCondition;
use YaangVu\LaravelBase\Base\Utility\Query\HasSelection;
use YaangVu\LaravelBase\Base\Utility\Query\Pageable;
use YaangVu\LaravelBase\Base\Utility\Query\Sortable;

/**
 * @see  ParamHandler, HasCondition, Pageable, Sortable, HasSelection
 *
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static int getLimit()
 * @link Pageable::getLimit()
 *
 * @method static ParamHandler setLimit(int $limit)
 * @link Pageable::setLimit()
 *
 * @method static int getPage()
 * @link Pageable::getPage()
 *
 * @method static ParamHandler setPage(int $page)
 * @link Pageable::setPage()
 *
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static ParamHandler parseSort(string $sortString)
 * @link Sortable::parseSort()
 *
 * @method static ParamHandler addSort(Sort $sort)
 * @link Sortable::addSort()
 *
 * @method static Sort[] getSorts()
 * @link Sortable::getSorts()
 *
 * @method static ParamHandler setSorts(Sort[] $sorts)
 * @link Sortable::setSorts()
 *
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static ParamHandler parseCondition(string $key, mixed $value)
 * @link HasCondition::parseCondition()
 *
 * @method static Condition[] getConditions()
 * @link HasCondition::getConditions()
 *
 * @method static ParamHandler setConditions(Condition[] $conditions)
 * @link HasCondition::setConditions()
 *
 * @method static string getSeparator()
 * @link HasCondition::getSeparator()
 *
 * @method static ParamHandler setSeparator(string $separator)
 * @link HasCondition::setSeparator()
 *
 * @method static bool nullableValue()
 * @link HasCondition::nullableValue()
 *
 * @method static ParamHandler setNullableValue(bool $nullableValue)
 * @link HasCondition::setNullableValue()
 *
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static ParamHandler parseSelections(string|array $selections)
 * @link HasSelection::parseSelections()
 *
 * @method static string|array getSelections()
 * @link HasSelection::getSelections()
 *
 * @method static ParamHandler setSelections(array|string $selections)
 * @link HasSelection::parseSelections()
 *
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static ParamHandler parseParams(array $params = [])
 * @link ParamHandler::parseParams()
 *
 * @method static ParamHandler parseParam(string $key, mixed $value)
 * @link ParamHandler::parseParam()
 *
 * @method static array getExcludedKeys()
 * @link ParamHandler::getExcludedKeys()
 *
 * @method static ParamHandler setExcludedKeys(string[] $excludedKeys)
 * @link ParamHandler::setExcludedKeys()
 *
 * @method static ParamHandler exclude(string $key)
 * @link ParamHandler::exclude()
 * |---------------------------------------------------------------------------------------------------------
 */
class Param extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ParamHandler::class;
    }
}