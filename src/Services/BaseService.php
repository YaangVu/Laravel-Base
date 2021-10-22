<?php

namespace YaangVu\LaravelBase\Services;

use Exception;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use YaangVu\Exceptions\BadRequestException;
use YaangVu\Exceptions\NotFoundException;
use YaangVu\Exceptions\SystemException;
use YaangVu\LaravelBase\Facades\Query;
use YaangVu\LaravelBase\Helpers\QueryHelper\QueryHelper;

abstract class BaseService
{
    public Model|Builder $model;

    protected static object|null $currentUser = null;

    public static Query|QueryHelper $query;

    protected array  $fillAbles;
    protected array  $guarded;
    protected string $primaryKey;
    protected string $driver;
    protected string $table;

    public function __construct()
    {
        $this->initModel();

        // Get attributes from model
        $this->fillAbles  = $this->model->getFillable();
        $this->guarded    = $this->model->getGuarded();
        $this->primaryKey = $this->model->getKeyName();
        $this->table      = $this->model->getTable();

        // Initial Query Facade
        $this->driver = $this->model->getConnection()->getDriverName();
        self::$query  = Query::driver($this->driver);
    }

    /**
     * Set Database Driver for query
     * @Author yaangvu
     * @Date   Jul 30, 2021
     *
     * @param string|null $driver
     *
     * @return BaseService
     */
    public function driver(?string $driver): BaseService
    {
        self::$query = Query::driver($driver);

        return $this;
    }

    /**
     * Create new Model
     * @return void
     */
    abstract function initModel(): void;

    /**
     * Get list of all items
     *
     * @param bool $paginated
     *
     * @return LengthAwarePaginator|Collection|array
     */
    public function getAll(bool $paginated = true): LengthAwarePaginator|Collection|array
    {
        $data = self::$query->buildQuery($this->model);
        try {
            $response = $paginated ? $data->paginate(self::$query->limit()) : $data->get();
            $this->postGetAll($response);

            return $response;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Get Entity via id
     *
     * @param int|string $id
     *
     * @return Model
     */
    public function get(int|string $id): Model
    {
        try {
            if (self::$query->getRelations())
                $this->model = $this->model->with(self::$query->getRelations());

            $entity = $this->model->findOrFail($id);
            $this->postGet($id, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Get Entity via uuid
     *
     * @param string $uuid
     *
     * @return Model
     */
    public function getByUuid(string $uuid): Model
    {
        try {
            if (self::$query->getRelations())
                $this->model = $this->model->with(self::$query->getRelations());

            $entity = $this->model->where('uuid', $uuid)->firstOrFail();
            $this->postGetByUuid($uuid, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $uuid"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @param string $uuid
     * @param Model  $model
     */
    public function postGetByUuid(string $uuid, Model $model)
    {
        // TODO: Implement postGetByUuid() method.
    }

    /**
     * Delete an Entity via ID
     *
     * @param int|string $id
     * @param bool       $transaction
     *
     * @return bool
     */
    public function delete(int|string $id, bool $transaction = false): bool
    {
        if ($transaction) DB::beginTransaction();

        $data = $this->get($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);
            if ($transaction) DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();

            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        }
    }

    /**
     * Delete a Entity via uuid
     *
     * @param string $uuid
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByUuid(string $uuid, bool $transaction = false): bool
    {
        if ($transaction) DB::beginTransaction();
        $data = $this->getByUuid($uuid);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuid($uuid);
            if ($transaction) DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $uuid"],
                $e
            );
        }
    }

    /**
     * Store new Entity
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return Model
     */
    public function add(object $request, bool $transaction = false): Model
    {
        if ($transaction) DB::beginTransaction();

        // Validate
        if ($this->validateStoreRequest($request) !== true)
            return $this->model;

        $requestArr = $this->_toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->fillAbles === ['*']
                || (in_array($column, $this->fillAbles) && !in_array($column, $this->guarded))
            )
                $this->model->{$column} = $this->_handleRequestValue($value);
        }

        // Set created_by is current user
        if (!str_contains($this->driver, 'sql') || Schema::hasColumn($this->table, 'created_by'))
            $this->model->created_by = self::currentUser()?->{$this->primaryKey} ?? null;

        // Set default uuid
        if (!str_contains($this->driver, 'sql') || Schema::hasColumn($this->table, 'uuid'))
            $this->model->uuid = $request->uuid ?? Uuid::uuid();

        try {
            $this->model->save();
            $this->postAdd($request, $this->model);
            if ($transaction) DB::commit();

            return $this->model;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();

            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return Model
     */
    public function update(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction) DB::beginTransaction();

        // Validate
        if ($this->validateUpdateRequest($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        $requestArr = $this->_toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->fillAbles === ['*']
                || (in_array($column, $this->fillAbles) && !in_array($column, $this->guarded))
            )
                $model->{$column} = $this->_handleRequestValue($value);
        }

        try {
            $model->save();
            $this->postUpdate($id, $request, $model);
            if ($transaction) DB::commit();

            return $model;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Put Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return Model
     */
    public function putUpdate(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction) DB::beginTransaction();

        // Validate
        if ($this->validateUpdateRequest($id, $request) !== true)
            return $this->model;

        $model    = $this->get($id);
        $modelArr = $model->toArray();

        foreach ($modelArr as $column => $value) {
            if ($column === $this->primaryKey || $column === 'uuid' || $column === 'created_by')
                continue;

            if ($request->{$column} !== null)
                $model->{$column} = $this->_handleRequestValue($request->{$column});
            else
                $model->{$column} = null;
        }

        try {
            $model->save();
            $this->postPutUpdate($id, $request, $model);
            if ($transaction) DB::commit();

            return $model;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByIds(object $request, bool $transaction = false): bool
    {
        if ($transaction) DB::beginTransaction();
        $this->doValidate($request, ['ids' => 'required']);
        $ids = explode(',', $request->ids ?? '');

        $data = $this->model->whereIn($this->primaryKey, $ids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByIds($request);
            if ($transaction) DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();

            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via Uuids
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByUuids(object $request, bool $transaction = false): bool
    {
        if ($transaction) DB::beginTransaction();
        $uuids = explode(',', $request->uuids ?? '');

        $data = $this->model->whereIn('uuid', $uuids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuids($request);
            if ($transaction) DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction) DB::rollBack();

            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Validate store request
     *
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     * @param array  $customAttributes
     *
     * @return bool|array
     */
    public function validateStoreRequest(object $request, array $rules = [], array $messages = [],
                                         array  $customAttributes = []): bool|array
    {
        if (!$rules)
            return true;

        return $this->doValidate($request, $rules, $messages, $customAttributes);
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     * @param array      $messages
     * @param array      $customAttributes
     *
     * @return bool|array
     */
    public function validateUpdateRequest(int|string $id, object $request, array $rules = [],
                                          array      $messages = [], array $customAttributes = []): bool|array
    {
        if (!$rules || !$id)
            return true;

        return $this->doValidate($request, $rules, $messages, $customAttributes);
    }

    /**
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     * @param array  $customAttributes
     * @param bool   $throwable
     *
     * @return bool|array
     */
    public static function doValidate(object $request, array $rules = [], array $messages = [],
                                      array  $customAttributes = [], bool $throwable = true): bool|array
    {
        if ($request instanceof Request || $request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules, $messages, $customAttributes);

        // If you have no rules were violated
        if (!$validator?->fails())
            return true;

        if ($throwable)
            throw new BadRequestException(['messages' => $validator->errors()->toArray()], new Exception());
        else
            return $validator->errors()->toArray();
    }

    /**
     * Set relation
     *
     * @param array|string $relations
     */
    public function with(array|string $relations)
    {
        self::$query->with($relations);
    }

    /**
     * Get Current User logged in
     */
    public static function currentUser(): object|null
    {
        return self::$currentUser ?? Auth::user();
    }

    /**
     * @param object $user
     *
     * @return void
     */
    public static function setCurrentUser(object $user): void
    {
        self::$currentUser = $user;
    }

    /**
     * @param object $request
     * @param Model  $model
     */
    public function postAdd(object $request, Model $model)
    {
        // TODO: Implement postAdd() method.
    }

    /**
     * @param int|string $id
     * @param Model      $model
     */
    public function postGet(int|string $id, Model $model)
    {
        // TODO: Implement postGet() method.
    }

    /**
     * @param LengthAwarePaginator|Collection|array $model
     */
    public function postGetAll(LengthAwarePaginator|Collection|array $model)
    {
        // TODO: Implement postGetAll() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postUpdate(int|string $id, object $request, Model $model)
    {
        // TODO: Implement postUpdate() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postPutUpdate(int|string $id, object $request, Model $model)
    {
        // TODO: Implement postUpdate() method.
    }

    /**
     * @param int|string $id
     */
    public function postDelete(int|string $id)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param string $uuid
     */
    public function postDeleteByUuid(string $uuid)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param object $request
     */
    public function postDeleteByIds(object $request)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param object $request
     */
    public function postDeleteByUuids(object $request)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function _handleRequestValue(mixed $value): mixed
    {
        if (gettype($value) === 'string')
            return trim($value);
        else
            return $value;
    }

    /**
     * Convert $request to array
     *
     * @Author yaangvu
     * @Date   Jul 31, 2021
     *
     * @param object $request
     *
     * @return array
     */
    protected function _toArray(object $request): array
    {
        if ($request instanceof Request || $request instanceof Model)
            return $request->toArray();
        else
            return (array)$request;
    }

    /**
     * @Description
     *
     * @Author yaangvu
     * @Date   Oct 22, 2021
     *
     * @param object $request
     * @param array  $data
     *
     * @return object
     */
    public function mergeRequestParams(object $request, array $data = []): object
    {
        if ($request instanceof Request || $request instanceof Model)
            return $request->merge($data);
        else {
            foreach ($data as $key => $value)
                $request->{$key} = $value;

            return $request;
        }
    }
}
