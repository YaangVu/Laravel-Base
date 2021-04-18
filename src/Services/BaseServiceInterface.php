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
     * Set relation
     *
     * @param string|array $relations
     */
    public function with(string|array $relations);

    /**
     * Get Current User logged in
     */
    public static function currentUser();

    /**
     * @param object $user
     */
    public static function setCurrentUser(object $user): void;
}
