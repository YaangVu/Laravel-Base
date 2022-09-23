<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use YaangVu\LaravelBase\Base\Query\Configurable;
use YaangVu\LaravelBase\Interfaces\ShouldCache;

trait Callback
{
    use HasEvent, Configurable;

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
    public function postGetAll(mixed $response): void
    {
        // Fire event if existed
        $events = $this->getAllSelectionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($response));

        // Cache data
        if ($this instanceof ShouldCache && !Cache::has($cachedKey = $this->getTable() . '-' . Request::serialize()))
            Cache::put($cachedKey, $response, min($this->getTtl(), 3600));
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
    public function postGet(int|string $id, Model $model): void
    {
        // Fire event if existed
        $events = $this->getSelectionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($id, $model));

        if ($this instanceof ShouldCache && !Cache::has($cachedKey = $this->getTable() . "-$id"))
            Cache::put($cachedKey, $model, $this->getTtl());
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
    public function postGetByUuid(string $uuid, Model $model): void
    {
        // Fire event if existed
        $events = $this->getUuidSelectionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($uuid, $model));

        if ($this instanceof ShouldCache && !Cache::has($cachedKey = $this->getTable() . "-uuid-$uuid"))
            Cache::put($cachedKey, $model, $this->getTtl());
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
        // Fire event if existed
        $events = $this->getAdditionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($request, $model));

        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->getTable() . "-$model->id", $model, $this->getTtl());
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
        // Fire event if existed
        $events = $this->getPatchEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($id, $request, $model));

        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->getTable() . "-$model->id", $model, $this->getTtl());
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
        // Fire event if existed
        $events = $this->getPutEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($id, $request, $model));

        // Cache data
        if ($this instanceof ShouldCache)
            Cache::put($this->getTable() . "-$model->id", $model, $this->getTtl());
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
        // Fire event if existed
        $events = $this->getIdDeletionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($id));

        // Remove Cached data
        if ($this instanceof ShouldCache)
            Cache::forget($this->getTable() . "-$id");
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
        // Fire event if existed
        $events = $this->getUuidDeletionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($uuid));

        // Remove Cached data
        if ($this instanceof ShouldCache)
            Cache::forget($this->getTable() . "-uuid-$uuid");
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
        // Fire event if existed
        $events = $this->getIdsDeletionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($request));

        // Remove Cached data
        if ($this instanceof ShouldCache) {
            $ids = explode(',', $request->ids ?? '');
            foreach ($ids as $id)
                Cache::forget($this->getTable() . "-$id");
        }

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
        // Fire event if existed
        $events = $this->getUuidsDeletionEvents();
        if (is_string($events))
            $events = [$events];
        foreach ($events as $event)
            event(new $event($request));

        // Remove Cached data
        if ($this instanceof ShouldCache) {
            $uuids = explode(',', $request->uuids ?? '');
            foreach ($uuids as $uuid)
                Cache::forget($this->getTable() . "-uuid-$uuid");
        }
    }
}
