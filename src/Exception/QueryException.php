<?php

namespace YaangVu\LaravelBase\Exception;

use Symfony\Component\HttpFoundation\Response;

class QueryException extends BaseException
{
    public int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
}
