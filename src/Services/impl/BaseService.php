<?php

namespace YaangVu\LaravelBase\Services\impl;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use YaangVu\Exceptions\BadRequestException;
use YaangVu\Exceptions\NotFoundException;
use YaangVu\Exceptions\SystemException;
use YaangVu\LaravelBase\Helpers\QueryHelper;
use YaangVu\LaravelBase\Services\BaseServiceInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\inArray;

abstract class BaseService implements BaseServiceInterface
{

    public static bool $validateThrowAble = true;

    protected QueryHelper $queryHelper;

    public Model|Builder $model;

    public static object|null $currentUser = null;

    public function __construct()
    {
        $this->createModel();
        $this->queryHelper = new QueryHelper($this->model->getConnection()->getDriverName());
    }

    /**
     * Create new Model
     * @return void
     */
    abstract function createModel(): void;

    /**
     * Get list of all items
     * @return LengthAwarePaginator
     * @throws SystemException
     */
    public function getAll(): LengthAwarePaginator
    {
        $this->preGetAll();
        $data = $this->queryHelper->buildQuery($this->model);
        try {
            $response = $data->paginate(QueryHelper::limit());
            $this->postGetAll($response);

            return $response;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Get Entity via Id
     *
     * @param int|string $id
     *
     * @return Model
     */
    public function get(int|string $id): Model
    {
        $this->preGet($id);
        try {
            if ($this->queryHelper->relations)
                $this->model = $this->model->with($this->queryHelper->relations);

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
     * Get Entity via Code
     *
     * @param string $code
     *
     * @return Model
     */
    public function getByCode(string $code): Model
    {
        $this->preGetByCode($code);
        try {
            if ($this->queryHelper->relations)
                $this->model = $this->model->with($this->queryHelper->relations);

            $codeField = $this->model->code;
            if (!Schema::hasColumn($this->model->getTable(), $codeField))
                throw new BadRequestException(__("not-exist", ['attribute' => __('entity')]));

            $entity = $this->model->where($codeField, $code)->firstOrFail();
            $this->postGetByCode($code, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $code"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @param string $code
     */
    public function preGetByCode(string $code)
    {
        // TODO: Implement preGetByCode() method.
    }

    /**
     * @param string $code
     * @param Model  $model
     */
    public function postGetByCode(string $code, Model $model)
    {
        // TODO: Implement postGetByCode() method.
    }

    /**
     * Delete a Entity via ID
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        DB::beginTransaction();
        $this->preDelete($id);
        $data = $this->get($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);
            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        }
    }

    /**
     * Delete a Entity via Code
     *
     * @param string $code
     *
     * @return bool
     */
    public function deleteByCode(string $code): bool
    {
        DB::beginTransaction();
        $this->preDeleteByCode($code);
        $data = $this->getByCode($code);
        try {
            $deleted = $data->delete();
            $this->postDeleteByCode($code);
            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $code"],
                $e
            );
        }
    }

    /**
     * Store new Entity
     *
     * @param object $request
     *
     * @return Model
     */
    public function add(object $request): Model
    {
        DB::beginTransaction();
        $request = $this->preAdd($request) ?? $request;

        // Validate
        if ($this->storeRequestValidate($request) !== true)
            return $this->model;

        // Set data to new entity
        $fillAbles = $this->model->getFillable();
        $guarded   = $this->model->getGuarded();
        if ($fillAbles === ['*']) { // Insert all data to DB
            if ($request instanceof Request)
                $requestArr = $request->toArray();
            else
                $requestArr = (array)$request;

            foreach ($requestArr as $column => $value)
                if (!inArray($column, $guarded))
                    $this->model->{$column} = $this->_handleRequestData($value);
        } else // Only insert specific data
            foreach ($fillAbles as $fillAble)
                if (isset($request->$fillAble) && !inArray($fillAble, $guarded))
                    $this->model->$fillAble = $this->_handleRequestData($request->$fillAble);

        // Set created_by is current user
        if (Schema::hasColumn($this->model->getTable(), 'created_by'))
            $this->model->created_by = self::currentUser()?->id ?? null;

        try {
            $this->model->save();
            $this->postAdd($request, $this->model);
            DB::commit();

            return $this->model;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     *
     * @return Model
     */
    public function update(int|string $id, object $request): Model
    {
        DB::beginTransaction();
        $request = $this->preUpdate($id, $request) ?? $request;

        // Validate
        if ($this->updateRequestValidate($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        // Set data for updated entity
        $fillAbles = $model->getFillable();
        $guarded   = $this->model->getGuarded();
        if ($fillAbles === ['*']) { // Insert all data to DB
            if ($request instanceof Request)
                $requestArr = $request->toArray();
            else
                $requestArr = (array)$request;

            foreach ($requestArr as $column => $value)
                if (!inArray($column, $guarded))
                    $model->{$column} = $this->_handleRequestData($value) ?? $model->{$column};
        } else
            foreach ($fillAbles as $fillAble)
                if (isset($request->$fillAble) && !inArray($fillAble, $guarded))
                    $model->$fillAble = $this->_handleRequestData($request->$fillAble) ?? $model->$fillAble;
        try {
            $model->save();
            $this->postUpdate($id, $request, $model);
            DB::commit();

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     *
     * @return bool
     */
    public function deleteByIds(object $request): bool
    {
        $this->preDeleteByIds($request);
        $this->doValidate($request, ['ids' => 'required|array']);
        $idField = $this->model->getKey();
        if (!Schema::hasColumn($this->model->getTable(), $idField))
            throw new BadRequestException(__("not-exist", ['attribute' => __('entity')]));

        $data = $this->model->whereIn($idField, $request->ids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByIds($request);

            return $deleted;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via Codes
     *
     * @param object $request
     *
     * @return bool
     */
    public function deleteByCodes(object $request): bool
    {
        $this->preDeleteByCodes($request);
        $this->doValidate($request, ['codes' => 'required|array']);
        $codeField = $this->model->code;
        if (!Schema::hasColumn($this->model->getTable(), $codeField))
            throw new BadRequestException(__("not-exist", ['attribute' => __('entity')]));

        $data = $this->model->whereIn($codeField, $request->codes);
        try {
            $deleted = $data->delete();
            $this->postDeleteByCodes($request);

            return $deleted;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Validate store request
     *
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    public function storeRequestValidate(object $request, array $rules = []): bool|array
    {
        if (!$rules || !$request)
            return true;

        return $this->doValidate($request, $rules);
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     *
     * @return bool|array
     */
    public function updateRequestValidate(int|string $id, object $request, array $rules = []): bool|array
    {
        if (!$rules || !$id || !$request)
            return true;

        return $this->doValidate($request, $rules);
    }

    /**
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    public static function doValidate(object $request, array $rules = []): bool|array
    {
        if ($request instanceof Request)
            $request = $request->all();
        elseif ($request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules);

        if ($validator?->fails()) {
            if (self::$validateThrowAble)
                throw new BadRequestException(['messages' => $validator->errors()->toArray()], new Exception());
            else
                return $validator->errors()->toArray();
        }

        return true;
    }

    /**
     * Set relation
     *
     * @param array|string $relations
     */
    public function with(array|string $relations)
    {
        $this->queryHelper->with($relations);
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
     */
    public function preAdd(object $request)
    {
        // TODO: Implement preAdd() method.
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
     */
    public function preGet(int|string $id)
    {
        // TODO: Implement preGet() method.
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
     */
    public function preGetAll()
    {
        // TODO: Implement preGetAll() method.
    }

    /**
     * @param object $model
     */
    public function postGetAll(object $model)
    {
        // TODO: Implement postGetAll() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     */
    public function preUpdate(int|string $id, object $request)
    {
        // TODO: Implement preUpdate() method.
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
     */
    public function preDelete(int|string $id)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param int|string $id
     */
    public function postDelete(int|string $id)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param string $code
     */
    public function preDeleteByCode(string $code)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param string $code
     */
    public function postDeleteByCode(string $code)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param object $request
     */
    public function preDeleteByIds(object $request)
    {
        // TODO: Implement preDelete() method.
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
    public function preDeleteByCodes(object $request)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param object $request
     */
    public function postDeleteByCodes(object $request)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function _handleRequestData(mixed $value): mixed
    {
        if (gettype($value) === 'string')
            return trim($value);
        else
            return $value;
    }

}
