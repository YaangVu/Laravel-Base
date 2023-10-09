<?php

namespace YaangVu\LaravelBase\Base\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasRequest
{
    /**
     * @param object   $request
     * @param string[] ...$params
     *
     * @return object
     */
    public function removeRequestParams(object $request, string|array $params): object
    {
        if (is_string($params))
            $params = [$params];

        $isHttpRequest = $request instanceof Request;

        foreach ($params as $param)
            if ($isHttpRequest) $request->request->remove($param);
            else unset($request->{$param});

        return $request;
    }

    /**
     * @param object               $request
     * @param array<string, mixed> $params
     *
     * @return object
     */
    public function mergeRequestParams(object $request, array $params): object
    {
        if ($request instanceof Request)
            $request->merge($params);
        else
            $request = (object)array_merge((array)$request, $params);

        return $request;
    }

    /**
     * @param object $request
     *
     * @return array
     */
    public function requestToArray(object $request): array
    {
        return ($request instanceof Request || $request instanceof Model) ? $request->toArray() : (array)$request;
    }
}