<?php
/**
 * @Author yaangvu
 * @Date   Aug 06, 2022
 */

namespace YaangVu\LaravelBase\Base;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Traits\Macroable;
use LogicException;
use Ramsey\Uuid\Uuid;
use YaangVu\LaravelBase\Base\Contract\Operator;
use YaangVu\LaravelBase\Base\Contract\Service;
use YaangVu\LaravelBase\Base\Contract\ShouldCache;
use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;
use YaangVu\LaravelBase\Base\Facade\Param;
use YaangVu\LaravelBase\Base\Utility\CanCast;
use YaangVu\LaravelBase\Base\Utility\HasRequest;
use YaangVu\LaravelBase\Base\Utility\ObjectToArray;
use YaangVu\LaravelBase\Base\Utility\Validatable;
use YaangVu\LaravelBase\Exception\NotFoundException;
use YaangVu\LaravelBase\Exception\QueryException;

class BaseService implements Service
{
    use Macroable, Validatable, CanCast, HasRequest, ObjectToArray;

    /**
     * Default value of each column, will be used in putUpdate() function
     *
     * @var array{string: string}
     */
    public array      $defaultValue = [];
    protected Builder $builder;
    /**
     * The fill able attributes
     *
     * @var string[]
     */
    protected array $fillAbles;
    /**
     * The guarded attributes
     *
     * @var string[]
     */
    protected array $guarded;
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected string $key;
    /**
     *  The table associated with the model.
     *
     * @var string
     */
    protected string $table;
    /**
     * The PDO driver name.
     *
     * @var string
     */
    protected string $driver;
    /**
     * Cache time to live in seconds
     *
     * @var int
     */
    protected int $ttl;
    /**
     * @var Operator
     */
    protected Operator $operator;

    /**
     * @var string
     */
    protected string $cacheTag;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(private readonly Model $model, private readonly ?string $alias = null)
    {
        $class           = get_class($model);
        $this->builder   = $class::query();
        $this->fillAbles = $model->getFillable();
        $this->guarded   = $model->getGuarded();
        $this->key       = $model->getKeyName();
        $this->table     = $model->getTable();
        $this->driver    = $model->getConnection()->getDriverName();
        $this->ttl       = config('laravel-base.cache.ttl');
        $this->operator  = app()->make(Operator::class, ['driver' => $this->driver]);
        $this->cacheTag  = $this->table;
    }

    /**
     * @inheritDoc
     */
    public function add(object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        $this->preAdd($request);

        // Validate request before process
        if ($this->validateStoreRequest($request) !== true)
            return $this->model;

        $requestArr = $this->toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->changeableColumn($column))
                $this->model->{$column} = $this->cast($value, $this->getCastType($column));
        }

        // If using NoSQL or SQL with table has created_by column, then Set created_by is current user
        if (!str_contains($this->driver, 'sql')
            || Schema::connection($this->model->getConnectionName())->hasColumn($this->table, 'created_by'))
            $this->model->setAttribute('created_by', Auth::id());

        // If using NoSQL or SQL with table has uuid column, then Set uuid
        if (!str_contains($this->driver, 'sql')
            || Schema::connection($this->model->getConnectionName())->hasColumn($this->table, 'uuid'))
            $this->model->setAttribute('uuid', $request->uuid ?? Uuid::uuid4());

        try {
            $this->model->save();

            $this->postAdd($request, $this->model);
            if ($transaction)
                DB::commit();

            return $this->model;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-add', ['attribute' => $this->table]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run add() function
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return object
     */
    public function preAdd(object &$request, bool $transaction = false): object
    {
        return $request;
    }

    /**
     * Check column can add or change
     *
     * @Author      yaangvu
     * @Date        Feb 28, 2023
     *
     * @param string $column
     *
     * @return bool
     */
    final function changeableColumn(string $column): bool
    {
        if (in_array($column, Param::getExcludedKeys()))
            return false;

        return $this->fillAbles === ['*']
               || (in_array($column, $this->fillAbles)
                   && !in_array($column, $this->guarded));
    }

    /**
     *  Do something after add the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param object $request
     * @param Model  $model
     */
    public function postAdd(object $request, Model $model): void
    {
        $id = $model->getAttribute($this->key);
        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->table . "-$id", $model, $this->ttl);
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function update(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        $this->preUpdate($id, $request);

        // Validate
        if ($this->validateUpdateRequest($id, $request) !== true)
            return $this->model;

        $model = $this->find($id);

        $requestArr = $this->toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->changeableColumn($column))
                $model->{$column} = $this->cast($value, $this->getCastType($column));
        }

        try {
            $model->save();

            $this->postUpdate($id, $request, $model);
            if ($transaction)
                DB::commit();

            return $model;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-update', ['attribute' => "$this->table: $id"]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run update() function
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return object
     */
    public function preUpdate(int|string $id, object &$request, bool $transaction = false): object
    {
        return $request;
    }

    /**
     * @inheritDoc
     */
    public function find(int|string $id): Model
    {
        if ($this instanceof ShouldCache && Cache::has($cachedKey = $this->table . ":$id:" . Request::serialize()))
            return Cache::get($cachedKey);

        $this->preFind($id);

        Param::parseParams();

        // Add Eager Loading
        $builder = Param::relate($this->builder);
        try {
            $entity = $builder->findOrFail($id, Param::getSelections());

            $this->postFind($id, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(message: __('laravel-base.not-found', ['attribute' => "$this->table: $id"]),
                previous:                        $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            // Do something
        }
    }

    /**
     * @inheritDoc
     */
    public function get(bool $paginated = true): LengthAwarePaginator|Collection
    {
        if ($this instanceof ShouldCache
            && Cache::tags($this->cacheTag)->has($cachedKey = $this->table . ':' . Request::serialize()))
            return Cache::tags($this->cacheTag)->get($cachedKey);

        Param::parseParams();

        $this->preGet($paginated);

        // if $this->>model has Searchable trait, then use search
        if (in_array("Laravel\Scout\Searchable", class_uses_recursive(get_class($this->model))) && Param::getKeyword())
            $builder = $this->model->search(Param::getKeyword())
                                   ->query(fn(Builder $builder) => $this->buildGetQuery($builder, false));
        else
            $builder = $this->buildGetQuery();

        try {
            $response = $paginated ? $builder->paginate(Param::getLimit()) : $builder->get();

            $this->postGet($response);

            return $response;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.query-error'), previous: $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            // Do something
        }
    }

    /**
     * Do something before run update() function
     *
     * @param bool $paginate
     *
     * @return mixed
     */
    public function preGet(bool $paginate = true): mixed
    {
        return null;
    }

    /**
     * Build the Builder for Get All Query
     *
     * @param Builder|null $builder
     * @param bool         $searchByKeyword
     *
     * @return Builder
     */
    public function buildGetQuery(?Builder $builder = null, bool $searchByKeyword = true): Builder
    {
        $builder ??= $this->builder;

        if ($this->alias)
            $builder->from($this->model->getTable(), $this->alias);

        // Add selections
        $builder->select(Param::getSelections());

        // Add Eager Loading
        $builder = Param::relate($builder);

        // Add where condition
        foreach (Param::getConditions() as $cond) {
            $operator = $this->operator->make($cond->getOperatorPattern());

            // If search LIKE, the $value will be "%$value%"
            if ($cond->getOperatorPattern() === OperatorPatternEnum::LIKE)
                $value = '%' . $cond->getValue() . '%';
            else // Else cast $value follow the $casts
                $value = $this->cast($cond->getValue(), $this->getCastType($cond->getColumn()));

            $builder = $this->filter($builder, $cond->getColumn(), $operator, $value);
        }

        // Add condition when has $keyword search
        if ($searchByKeyword)
            $builder = $this->filterByKeyword($builder, $this->operator->make(OperatorPatternEnum::LIKE));

        // Sort data
        foreach (Param::getSorts() as $sort)
            $builder->orderBy($sort->getColumn(), $sort->getType());

        return $builder;
    }

    /**
     * @param Builder      $builder
     * @param string|array $columns
     * @param string       $operator
     * @param mixed|null   $value
     *
     * @return Builder
     */
    protected function filter(Builder $builder, string|array $columns, string $operator = '=',
                              mixed   $value = null): Builder
    {
        // check if exists function customFilter then call it
        if (method_exists($this, 'customFilter'))
            return $this->customFilter($builder, $columns, $operator, $value);

        if (is_array($columns)) {
            $builder->where(function ($builder) use ($columns, $operator, $value) {
                foreach ($columns as $column) {
                    $builder->orWhere($column, $operator, $value);
                }
            });
        }
        else {
            $builder->where($columns, $operator, $value);
        }

        return $builder;
    }

    /**
     *  Do something after get the all records
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param mixed $response
     *
     * @return void
     */
    public function postGet(mixed $response): void
    {
        // Cache data
        if ($this instanceof ShouldCache
            && !Cache::tags($this->cacheTag)->has($cachedKey = $this->table . ':' . Request::serialize()))
            Cache::tags($this->cacheTag)->put($cachedKey, $response, min($this->ttl, 3600));
        // TODO
    }

    /**
     * Do something before run find() function
     *
     * @param int|string $id
     *
     * @return mixed
     */
    public function preFind(int|string $id): mixed
    {
        return null;
    }

    /**
     *  Do something after get the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param int|string $id
     * @param Model      $model
     */
    public function postFind(int|string $id, Model $model): void
    {
        if ($this instanceof ShouldCache && !Cache::has($cachedKey = $this->table . ":$id:" . Request::serialize()))
            Cache::put($cachedKey, $model, $this->ttl);
        // TODO
    }

    /**
     *  Do something after patch update the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postUpdate(int|string $id, object $request, Model $model): void
    {
        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->table . ":$id", $model, $this->ttl);
        // TODO
    }

    /**
     * Add keyword for a Builder search query
     *
     * @param Builder $builder
     * @param string  $operator
     *
     * @return Builder
     */
    public function filterByKeyword(Builder $builder, string $operator = 'like'): Builder
    {
        if (!Param::getKeyword() || !Param::getSearchKeys())
            return $builder;

        $keyword = Param::getKeyword();

        return $builder = $this->filter($builder, Param::getSearchKeys(), $operator, "%$keyword%");
    }

    /**
     * @inheritDoc
     */
    public function putUpdate(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        $this->prePutUpdate($id, $request, $transaction);

        // Validate
        if ($this->validatePutUpdateRequest($id, $request) !== true)
            return $this->model;

        $model = $this->find($id);
        $attrs = $model->getAttributes();

        foreach ($attrs as $column => $value) {
            // If is changable column and has exist data in request, then update
            if ($this->changeableColumn($column) && isset($request->{$column}))
                $model->{$column} = $this->cast($request->{$column}, $this->getCastType($column));
            elseif (!str_contains($this->driver, 'sql'))
                // if not, but DB connection is nosql, then remove old data
                unset($model->{$column});
            else
                // else set to default null value
                $model->{$column} = $this->defaultValue[$column] ?? null;
        }

        try {
            $model->save();

            $this->postPutUpdate($id, $request, $model);
            if ($transaction)
                DB::commit();

            return $model;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-update', ['attribute' => "$this->table: $id"]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run update() function
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return object
     */
    public function prePutUpdate(int|string $id, object &$request, bool $transaction = false): object
    {
        return $request;
    }

    /**
     *  Do something after put update the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postPutUpdate(int|string $id, object $request, Model $model): void
    {
        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->table . ":$id", $model, $this->ttl);
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();
        $data = $this->findByUuid($uuid);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuid($uuid);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-delete', ['attribute' => "$this->table: $uuid"]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid): Model
    {
        if ($this instanceof ShouldCache && Cache::has($cachedKey = $this->table . ":uuid:$uuid"))
            return Cache::get($cachedKey);

        $this->preFindByUuid($uuid);

        Param::parseParams();

        try {
            $entity = $builder->where('uuid', '=', $uuid)->firstOrFail(Param::getSelections());

            $this->postFindByUuid($uuid, $entity);

            return $entity;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.not-found', ['attribute' => "$this->table: $uuid"]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            // Do something
        }
    }

    /**
     * Do something before run findByUuid() function
     *
     * @param string $uuid
     *
     * @return mixed
     */
    public function preFindByUuid(string $uuid): mixed
    {
        // Do something
        return null;
    }

    /**
     *  Do something after get the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param string $uuid
     * @param Model  $model
     */
    public function postFindByUuid(string $uuid, Model $model): void
    {
        if ($this instanceof ShouldCache && !Cache::has($cachedKey = $this->table . ":uuid:$uuid"))
            Cache::put($cachedKey, $model, $this->ttl);
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function delete(int|string $id, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();

        $this->preDelete($id, $transaction);

        $data = $this->find($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (LogicException|\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-delete', ['attribute' => "$this->table: $id"]),
                previous:                     $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run delete() function
     *
     * @param int|string $id
     * @param bool       $transaction
     *
     * @return object
     */
    public function preDelete(int|string $id, bool $transaction = false): mixed
    {
        return null;
    }

    /**
     *  Do something after delete the record
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param int|string $id
     */
    public function postDelete(int|string $id): void
    {
        // Remove Cached data
        if ($this instanceof ShouldCache) {
            Cache::forget($this->table . ":$id");
            Cache::tags($this->cacheTag)->flush();
        }
        // TODO
    }

    /**
     *  Do something after delete the record by Uuid
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param string $uuid
     */
    public function postDeleteByUuid(string $uuid): void
    {
        // Remove Cached data
        if ($this instanceof ShouldCache) {
            Cache::forget($this->table . ":uuid:$uuid");
            Cache::tags($this->cacheTag)->flush();
        }
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function deleteByIds(object $request, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();

        $this->preDeleteByIds($request, $transaction);
        $this->doValidate($request, ['ids' => 'required']);
        $ids = explode(',', $request->ids ?? '');

        $data = $this->model->query()->whereIn($this->key, $ids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByIds($request);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(message: __('laravel-base.can-not-delete',
                                                 ['attribute' => "$this->table: $request->ids"]), previous: $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run deleteByIds() function
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return object
     */
    public function preDeleteByIds(object &$request, bool $transaction = false): object
    {
        return $request;
    }

    /**
     *  Do something after delete multiple records by Ids list
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param object $request
     */
    public function postDeleteByIds(object $request): void
    {
        // Remove Cached data
        if ($this instanceof ShouldCache) {
            $ids = explode(',', $request->ids ?? '');
            foreach ($ids as $id) {
                Cache::forget($this->table . ":$id");
                Cache::tags($this->cacheTag)->flush();
            }
        }
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuids(object $request, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();

        $this->preDeleteByUuids($request, $transaction);

        $uuids = explode(',', $request->uuids ?? '');

        $data = $this->model->query()->whereIn('uuid', $uuids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuids($request);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (\Illuminate\Database\QueryException|LogicException $e) {
            throw new QueryException(message: __('laravel-base.can-not-del',
                                                 ['attribute' => "$this->table: $request->uuids"]), previous: $e);
        } catch (Exception $e) {
            throw new QueryException(message: __('laravel-base.server-error'), previous: $e);
        } finally {
            if ($transaction)
                DB::rollBack();
        }
    }

    /**
     * Do something before run deleteByUuids() function
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return object
     */
    public function preDeleteByUuids(object &$request, bool $transaction = false): object
    {
        return $request;
    }

    /**
     *  Do something after delete multiple records by Uuids list
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     * @param object $request
     */
    public function postDeleteByUuids(object $request): void
    {
        // Remove Cached data
        if ($this instanceof ShouldCache) {
            $uuids = explode(',', $request->uuids ?? '');
            foreach ($uuids as $uuid) {
                Cache::forget($this->table . ":uuid:$uuid");
                Cache::tags($this->cacheTag)->flush();
            }
        }
        // TODO
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @return string
     */
    public function getCacheTag(): string
    {
        return $this->cacheTag;
    }

}
