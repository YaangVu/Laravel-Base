<?php

namespace YaangVu\LaravelBase\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends BaseException
{
    public int $statusCode = Response::HTTP_NOT_FOUND;
}
