<?php

namespace YaangVu\LaravelBase\Services;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    /**
     * Create new Model
     * @return void
     */
    function createModel(): void;

    /**
     * Get list of all items
     */
    public function getAll(): LengthAwarePaginator;

    /**
     * Get Entity via Id
     *
     * @param int|string $id
     */
    public function get(int|string $id): Model;

    /**
     * Delete a Entity via ID
     *
     * @param int|string $id
     */
    public function delete(int|string $id): bool;

    /**
     * Store new Entity
     *
     * @param object $request
     */
    public function add(object $request): Model;

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     */
    public function update(int|string $id, object $request): Model;

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     *
     * @return bool
     */
    public function deleteByIds(object $request): bool;

    /**
     * Validate store request
     *
     * @param object $request
     */
    public function storeRequestValidate(object $request);

    /**
     * @param int|string $id
     * @param object     $request
     */
    public function updateRequestValidate(int|string $id, object $request);

    /**
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    public static function doValidate(object $request, array $rules = []): bool|array;

    /**
     * Set relation
     *
     * @param array|string $relations
     */
    public function with(array|string $relations);

    /**
     * Get Current User logged in
     */
    public static function currentUser();

    /**
     * @param object $user
     */
    public static function setCurrentUser(object $user): void;

    /**
     * @param object $request
     */
    public function preAdd(object $request);

    /**
     * @param object $request
     * @param Model  $model
     */
    public function postAdd(object $request, Model $model);

    /**
     * @param int|string $id
     * @param object     $request
     */
    public function preUpdate(int|string $id, object $request);

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postUpdate(int|string $id, object $request, Model $model);

    /**
     * @param int|string $id
     */
    public function preGet(int|string $id);

    /**
     * @param int|string $id
     * @param Model      $model
     */
    public function postGet(int|string $id, Model $model);

    /**
     */
    public function preGetAll();

    /**
     * @param object $model
     */
    public function postGetAll(object $model);

    /**
     * @param int|string $id
     */
    public function preDelete(int|string $id);

    /**
     * @param int|string $id
     */
    public function postDelete(int|string $id);

    /**
     * @param object $request
     */
    public function preDeleteByIds(object $request);

    /**
     * @param object $request
     */
    public function postDeleteByIds(object $request);

}
