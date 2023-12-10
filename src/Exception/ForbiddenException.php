<?php

namespace YaangVu\LaravelBase\Exception;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends BaseException
{
    public int $statusCode = Response::HTTP_FORBIDDEN;
}
