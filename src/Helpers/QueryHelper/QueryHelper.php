<?php
/**
 * @Author yaangvu
 * @Date   Oct 22, 2021
 */

namespace YaangVu\LaravelBase\Helpers\QueryHelper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface QueryHelper
{
    /**
     * @return string
     */
    public function getSeparator(): string;

    /**
     * @param string $separator
     *
     * @return QueryHelper
     */
    public function setSeparator(string $separator): static;

    /**
     * Get list Operators
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getOperators(): array;

    /**
     * Set Operators
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operators
     *
     * @return QueryHelper
     */
    public function setOperators(array $operators = []): static;

    /**
     * Set Operator patterns
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operatorPatterns
     *
     * @return QueryHelper
     */
    public function setOperatorPatterns(array $operatorPatterns = []): static;

    /**
     * Get Parameters
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Set params for query
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params = []): static;


    /**
     * Add one more condition
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return QueryHelper
     */
    public function addParam(string $param, mixed $value): static;

    /**
     * Remove one condition
     *
     * @param string $param
     *
     * @return QueryHelper
     */
    public function removeParam(string $param): static;

    /**
     * Get Cast parameters
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getCastParams(): array;

    /**
     * Set cast params
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $params
     *
     * @return $this
     */
    public function setCastParams(array $params = []): static;

    /**
     * Add one more cast param
     *
     * @param string $param
     * @param mixed  $type
     *
     * @return QueryHelper
     */
    public function addCastParam(string $param, mixed $type): static;


    /**
     * Remove cast param
     *
     * @param string $param
     *
     * @return QueryHelper
     */
    public function removeCastParam(string $param): static;

    /**
     * Get Excluded Operators
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return string[]
     */
    public function getExcludedOperators(): array;

    /**
     * Set cast params
     *
     * @Author yaangvu
     * @Date   Jul 29, 2021
     *
     * @param array $operators
     *
     * @return $this
     */
    public function setExcludedOperators(array $operators = []): static;

    /**
     * Add more exclude operators
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param ...$operator
     *
     * @return $this
     */
    public function addExcludedOperators(...$operator): static;

    /**
     * Remove exclude operator
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param string $operator
     *
     * @return $this
     */
    public function removeExcludedOperators(string $operator): static;

    /**
     * Get all conditions from Request
     *
     * @param array $params
     *
     * @return array
     */
    public function getConditions(array $params = []): array;

    /**
     * Get limit records
     *
     * @return int
     */
    public static function limit(): int;

    /**
     * Get Order by column and type
     *
     * @return array|null
     */
    public function getOrderBy(): ?array;

    /**
     * Add conditions and order by
     *
     * @param Model|Builder $model
     *
     * @param String        $alias
     *
     * @return Builder|Model
     */
    public function buildQuery(Model|Builder $model, string $alias = ''): Builder|Model;

    /**
     * Add relations for query
     *
     * @param string|array $relations
     */
    public function with(...$relations): static;

    /**
     * Get relationships
     *
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @return array
     */
    public function getRelations(): array;
}