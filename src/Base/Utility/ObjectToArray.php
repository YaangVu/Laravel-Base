<?php

namespace YaangVu\LaravelBase\Base\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait ObjectToArray
{
    /**
     * Convert an object to array
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
}