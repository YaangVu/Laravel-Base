<?php

namespace YaangVu\LaravelBase\Base\Utility;

use Illuminate\Http\Request;

trait HasRequest
{
    /**
     * @param object $request
     * @param        ...$params
     *
     * @return Request|object|mixed
     */
    public function removeParams(object $request, ...$params): mixed
    {
        if ($request instanceof Request)
            $request->except($params);
        else {
            foreach ($params as $param)
                unset($request->{$param});
        }

        return $request;
    }
}