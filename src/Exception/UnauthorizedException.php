<?php

namespace YaangVu\LaravelBase\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends BaseException
{
    public int $statusCode = Response::HTTP_UNAUTHORIZED;
}
