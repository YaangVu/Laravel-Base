<?php

namespace YaangVu\LaravelBase\Services\impl;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
use YaangVu\LaravelBase\Exceptions\BadRequestException;
use YaangVu\LaravelBase\Exceptions\NotFoundException;
use YaangVu\LaravelBase\Exceptions\SystemException;
use YaangVu\LaravelBase\Helpers\QueryHelper;
use YaangVu\LaravelBase\Services\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{

    public bool $validateThrowAble = true;

    protected QueryHelper $queryHelper;

    public Model $model;

    public static object $currentUser;

    public function __construct()
    {
        $this->queryHelper = new QueryHelper();
        $this->createModel();
        self::$currentUser = new stdClass();
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
        $data = $this->queryHelper->buildQuery($this->model);
        try {
            return $data->paginate(QueryHelper::limit());
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
        try {
            return $this->model->findOrFail($id);
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
     * Delete a Entity via ID
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $data = $this->get($id);
        try {
            return $data->delete();
        } catch (Exception $e) {
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
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
        if ($this->storeRequestValidate($request) !== true)
            return $this->model;

        $fillAbles = $this->model->getFillable();
        // Set data to new entity
        foreach ($fillAbles as $fillAble)
            $this->model->$fillAble = $request->$fillAble ?? null;
        // Set created_by is current user
        $this->model->created_by = self::currentUser()?->id ?? null;

        try {
            $this->model->save();

            return $this->model;
        } catch (Exception $e) {
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
        if ($this->updateRequestValidate($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        $fillAbles = $model->getFillable();
        foreach ($fillAbles as $fillAble)
            $model->$fillAble = $request->$fillAble ?? $model->$fillAble;
        try {
            $model->save();

            return $model;
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

        return $this->_doValidate($request, $rules);
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

        return $this->_doValidate($request, $rules);
    }

    /**
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    private function _doValidate(object $request, array $rules = []): bool|array
    {
        if ($request instanceof Request)
            $request = $request->all();
        elseif ($request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules);

        if ($validator?->fails()) {
            if ($this->validateThrowAble)
                throw new BadRequestException($validator->errors()->toArray(), new Exception());
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
    public static function currentUser(): object
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
     *
     * @return mixed
     */
    public function preAdd(object $request): mixed
    {
        // TODO: Implement preAdd() method.
    }

    /**
     * @param object $request
     * @param Model  $model
     *
     * @return mixed
     */
    public function postAdd(object $request, Model $model): mixed
    {
        // TODO: Implement postAdd() method.
    }

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function preGet(int|string $id): mixed
    {
        // TODO: Implement preGet() method.
    }

    /**
     * @param int|string $id
     * @param Model      $model
     *
     * @return mixed
     */
    public function postGet(int|string $id, Model $model): mixed
    {
        // TODO: Implement postGet() method.
    }

    /**
     * @return mixed
     */
    public function preGetAll(): mixed
    {
        // TODO: Implement preGetAll() method.
    }

    /**
     * @param Model $model
     *
     * @return mixed
     */
    public function postGetAll(Model $model): mixed
    {
        // TODO: Implement postGetAll() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     *
     * @return mixed
     */
    public function preUpdate(int|string $id, object $request): mixed
    {
        // TODO: Implement preUpdate() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     *
     * @return mixed
     */
    public function postUpdate(int|string $id, object $request, Model $model): mixed
    {
        // TODO: Implement postUpdate() method.
    }

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function preDelete(int|string $id): mixed
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function postDelete(int|string $id): mixed
    {
        // TODO: Implement postDelete() method.
    }
}
