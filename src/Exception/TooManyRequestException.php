<?php

namespace YaangVu\LaravelBase\Exception;

use Symfony\Component\HttpFoundation\Response;

class TooManyRequestException extends BaseException
{
    public int $statusCode = Response::HTTP_TOO_MANY_REQUESTS;
}
