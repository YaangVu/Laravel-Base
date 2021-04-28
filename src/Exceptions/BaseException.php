<?php

namespace YaangVu\LaravelBase\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class BaseException extends HttpResponseException
{
    public function __construct(string|array $message, Exception $e, int $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
//        app('sentry')->captureException($e);
        if (!is_array($message))
            $message = ['message' => $message];

        Log::debug("BaseException debug: $e \n --------------> Messages: ", $message);

        if (env('APP_ENV') != 'production' && $e !== null) {
            $message['error'] = $e->getMessage() ?? '';
            $message['code']  = $e->getCode() ?? '';
            $message['file']  = $e->getFile() ?? '';
            $message['line']  = $e->getLine() ?? '';
            $message['trace'] = $e->getTraceAsString() ?? '';
        }

        $response = response()->json($message)->setStatusCode($code);

        parent::__construct($response);
    }
}
