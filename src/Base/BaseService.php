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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Traits\Macroable;
use Ramsey\Uuid\Uuid;
use YaangVu\LaravelBase\Base\Contract\Operator;
use YaangVu\LaravelBase\Base\Contract\Service;
use YaangVu\LaravelBase\Base\Contract\ShouldCache;
use YaangVu\LaravelBase\Base\Enum\OperatorPatternEnum;
use YaangVu\LaravelBase\Base\Facade\Param;
use YaangVu\LaravelBase\Base\Utility\CanCast;
use YaangVu\LaravelBase\Base\Utility\HasRequest;
use YaangVu\LaravelBase\Base\Utility\Validatable;
use YaangVu\LaravelBase\Exception\QueryException;
use YaangVu\LaravelBase\Exception\SystemException;
class BaseService implements Service
{
    use Macroable, Validatable, CanCast, HasRequest;

    protected Builder $builder;

    /**
     * The fill able attributes
     *
     * @var string[]
     */
    private array $fillAbles;

    /**
     * The guarded attributes
     *
     * @var string[]
     */
    private array $guarded;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    private string $key;

    /**
     *  The table associated with the model.
     *
     * @var string
     */
    private string $table;

    /**
     * The PDO driver name.
     *
     * @var string
     */
    private string $driver;

    /**
     * Cache time to live in seconds
     *
     * @var int
     */
    private int $ttl;

    /**
     * @var Operator
     */
    private Operator $operator;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(private readonly Model $model, private readonly ?string $alias = null)
    {
        $this->builder   = $model->newQuery();
        $this->fillAbles = $model->getFillable();
        $this->guarded   = $model->getGuarded();
        $this->key       = $model->getKeyName();
        $this->table     = $model->getTable();
        $this->driver    = $model->getConnection()->getDriverName();
        $this->ttl       = config('laravel-base.cache.ttl');
        $this->operator  = app()->make(Operator::class, ['driver' => $this->driver]);
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException($e->getMessage() ?? __('laravel-base.server-error'), $e);
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
     * Convert request to array
     *
     * @Author yaangvu
     * @Date   Aug 07, 2022
     *
     * @param object $request
     *
     * @return array
     */
    final function toArray(object $request): array
    {
        if ($request instanceof Request || $request instanceof Model)
            return $request->toArray();
        else
            return (array)$request;
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
               || (in_array($column, $this->fillAbles) && !in_array($column, $this->guarded));
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
            Cache::tags($this->table)->put($this->table . "-$id", $model, $this->ttl);
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('laravel-base.server-error'), $e);
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
        if ($this instanceof ShouldCache && Cache::tags($this->table)->has($cachedKey = $this->table . "-$id"))
            return Cache::tags($this->table)->get($cachedKey);

        $this->preFind($id);

        Param::parseParams();

        // Add Eager Loading
        $this->builder = Param::relate($this->builder);

        $entity = $this->builder->findOrFail($id, Param::getSelections());

        $this->postFind($id, $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function get(bool $paginated = true): LengthAwarePaginator|Collection
    {
        if ($this instanceof ShouldCache && Cache::tags($this->table)->has($cachedKey = $this->table . '-' . Request::serialize()))
            return Cache::tags($this->table)->get($cachedKey);

        $this->preGet($paginated);

        $this->buildGetQuery();

        try {
            $response = $paginated ? $this->builder->paginate(Param::getLimit()) : $this->builder->get();

            $this->postGet($response);

            return $response;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('laravel-base.server-error'), $e);
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
     * @return Builder
     */
    public function buildGetQuery(): Builder
    {
        Param::parseParams();

        if ($this->alias)
            $this->builder->from($this->model->getTable(), $this->alias);

        // Add selections
        $this->builder->select(Param::getSelections());

        // Add Eager Loading
        $this->builder = Param::relate($this->builder);

        // Add where condition
        foreach (Param::getConditions() as $cond) {
            $operator = $this->operator->make($cond->getOperatorPattern());

            if ($cond->getOperatorPattern() === OperatorPatternEnum::LIKE)
                // If search LIKE, the $value will be "%$value%"
                $value = '%' . $cond->getValue() . '%';
            else // Else cast $value follow the $casts
                $value = $this->cast($cond->getValue(), $this->getCastType($cond->getColumn()));

            $this->builder->where($cond->getColumn(), $operator, $value);
        }

        // Add condition when has $keyword search
        $this->builder = Param::buildFindByKeyword($this->builder, $this->operator->make(OperatorPatternEnum::LIKE));

        // Sort data
        foreach (Param::getSorts() as $sort)
            $this->builder->orderBy($sort->getColumn(), $sort->getType());

        return $this->builder;
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
        if ($this instanceof ShouldCache && !Cache::tags($this->table)->has($cachedKey = $this->table . '-' . Request::serialize()))
            Cache::tags($this->table)->put($cachedKey, $response, min($this->ttl, 3600));
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
        if ($this instanceof ShouldCache && !Cache::tags($this->table)->has($cachedKey = $this->table . "-$id"))
            Cache::tags($this->table)->put($cachedKey, $model, $this->ttl);
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
            Cache::tags($this->table)->put($this->table . "-$id", $model, $this->ttl);
        // TODO
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

        //TODO

        $model = $this->find($id);
        // $modelArr = $model->toArray();
        //
        // foreach ($modelArr as $column => $value) {
        //     // Ignore data if it can be not modified
        //     if ($column === $this->key || $column === 'uuid' || $column === 'created_by')
        //         continue;
        //     if ($this->fillAbles === ['*'] || !in_array($column, $this->fillAbles))
        //         continue;
        //     if (in_array($column, $this->guarded))
        //         continue;
        //
        //     $model->{$column} = $this->cast($value, $this->getCastType($column));
        // }

        try {
            // $model->save();

            $this->postPutUpdate($id, $request, $model);
            if ($transaction)
                DB::commit();

            return $model;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('laravel-base.server-error'), $e);
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
            Cache::tags($this->table)->put($this->table . "-$id", $model, $this->ttl);
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException(
                [
                    'message' => __('laravel-base.can-not-del', ['attribute' => __('laravel-base.entity')]) . ": $uuid"
                ], $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function findByUuid(string $uuid): Model
    {
        if ($this instanceof ShouldCache && Cache::tags($this->table)->has($cachedKey = $this->table . "-uuid-$uuid"))
            return Cache::tags($this->table)->get($cachedKey);

        $this->preFindByUuid($uuid);

        Param::parseParams();

        $entity = $this->builder->where('uuid', '=', $uuid)->firstOrFail(Param::getSelections());

        $this->postFindByUuid($uuid, $entity);

        return $entity;
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
        if ($this instanceof ShouldCache && !Cache::tags($this->table)->has($cachedKey = $this->table . "-uuid-$uuid"))
            Cache::tags($this->table)->put($cachedKey, $model, $this->ttl);
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                [
                    'message' => __('laravel-base.can-not-del',
                                    ['attribute' => __('laravel-base.entity')]) . ": $id"
                ], $e
            );
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
        if ($this instanceof ShouldCache)
            Cache::tags($this->table)->flush();
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
        if ($this instanceof ShouldCache)
            Cache::tags($this->table)->flush();
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                [
                    'message' => __('laravel-base.can-not-del',
                                    ['attribute' => __('laravel-base.entity')]) . ": $request->ids"
                ], $e
            );
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
            foreach ($ids as $id)
                Cache::tags($this->table)->flush();
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
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                [
                    'message' => __('laravel-base.can-not-del',
                                    ['attribute' => __('laravel-base.entity')]) . ": $request->uuids"
                ], $e
            );
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
            foreach ($uuids as $uuid)
                Cache::tags($this->table)->flush();
        }
        // TODO
    }

}
