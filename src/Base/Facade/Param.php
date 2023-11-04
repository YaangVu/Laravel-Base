<?php
/**
 * @Author yaangvu
 * @Date   Feb 04, 2023
 */

namespace YaangVu\LaravelBase\Base\Facade;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;
use YaangVu\LaravelBase\Base\DataObject\Condition;
use YaangVu\LaravelBase\Base\DataObject\Sort;
use YaangVu\LaravelBase\Base\ParamHandler;
use YaangVu\LaravelBase\Base\Utility\Query\HasCondition;
use YaangVu\LaravelBase\Base\Utility\Query\HasRelationship;
use YaangVu\LaravelBase\Base\Utility\Query\HasKeywordSearch;
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
 * @method static ParamHandler parseSorts(string|string[] $sorts)
 * @link Sortable::parseSorts()
 *
 * @method static ParamHandler parseSort(string $sort)
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
 * @method static ParamHandler addCondition(Condition $condition)
 * @link HasCondition::addCondition()
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
 * @method static Builder buildFindByKeyword(Builder $builder, string $operator = 'like')
 * @link HasKeywordSearch::buildFindByKeyword()
 *
 * @method static mixed getKeyword()
 * @link HasKeywordSearch::getKeyword()
 *
 * @method static static setKeyword(mixed $keyword)
 * @link HasKeywordSearch::setKeyword()
 *
 * @method static array getSearchKeys()
 * @link HasKeywordSearch::getSearchKeys()
 *
 * @method static static setSearchKeys(array $searchKeys)
 * @link HasKeywordSearch::setSearchKeys()
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
 * @link HasSelection::setSelections()
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
 * @method static ParamHandler exclude(string ...$keys)
 * @link ParamHandler::exclude()
 * |---------------------------------------------------------------------------------------------------------
 *
 * @method static Builder relate(Builder $builder)
 * @link HasRelationship::relate()
 *
 * @method static Builder addWith(string|array $with)
 * @link HasRelationship::addWith()
 *
 * @method static Builder addWithCount(string|array $with)
 * @link HasRelationship::addWith()
 *
 * @method static Builder addWithAvg(string|array $with)
 * @link HasRelationship::addWith()
 *
 * @method static Builder addWithSum(string|array $with)
 * @link HasRelationship::addWith()
 *
 * @method static Builder addWithMax(string|array $with)
 * @link HasRelationship::addWith()
 *
 * @method static Builder addWithMin(string|array $with)
 * @link HasRelationship::addWith()
 *
 *  |---------------------------------------------------------------------------------------------------------
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