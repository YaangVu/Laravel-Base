<?php

namespace YaangVu\LaravelBase\Exception;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BaseException extends HttpResponseException
{
    protected bool $shouldCapture = false;

    public function __construct(string|array $message,
                                ?Exception   $e = null,
                                int          $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        if (is_null($e))
            $e = new Exception(is_string($message) ? $message : json_encode($message));

        if (!is_array($message))
            $message = ['message' => $message];

        Log::error("BaseException debug: $e \n --------------> Messages: ", $message);

        if (env('APP_ENV') != 'production') {
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
