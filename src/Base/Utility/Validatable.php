<?php
/**
 * @Author yaangvu
 * @Date   Aug 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use YaangVu\LaravelBase\Exception\BadRequestException;

trait Validatable
{
    /**
     * Validate store request
     *
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     * @param array  $customAttributes
     * @param bool   $throwable
     *
     * @return bool|array
     */
    public function validateStoreRequest(object $request, array $rules = [], array $messages = [],
                                         array  $customAttributes = [], bool $throwable = true): bool|array
    {
        if (!$rules)
            return true;

        return $this->doValidate($request, $rules, $messages, $customAttributes, $throwable);
    }

    /**
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     * @param array  $customAttributes
     * @param bool   $throwable
     *
     * @return bool|array
     */
    public static function doValidate(object $request, array $rules = [], array $messages = [],
                                      array  $customAttributes = [], bool $throwable = true): bool|array
    {
        if ($request instanceof Request || $request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules, $messages, $customAttributes);

        // If you have no rules were violated
        if (!$validator?->fails())
            return true;

        if ($throwable)
            throw new BadRequestException(['messages' => $validator->errors()->toArray()]);
        else
            return $validator->errors()->toArray();
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     * @param array      $messages
     * @param array      $customAttributes
     * @param bool       $throwable
     *
     * @return bool|array
     */
    public function validateUpdateRequest(int|string $id, object $request, array $rules = [], array $messages = [],
                                          array      $customAttributes = [], bool $throwable = true): bool|array
    {
        if (!$rules || !$id)
            return true;

        return $this->doValidate($request, $rules, $messages, $customAttributes, $throwable);
    }

    /**
     * Validate Put update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     * @param array      $messages
     * @param array      $customAttributes
     * @param bool       $throwable
     *
     * @return bool|array
     */
    public function validatePutUpdateRequest(int|string $id, object $request, array $rules = [], array $messages = [],
                                             array      $customAttributes = [], bool $throwable = true): bool|array
    {
        if (!$rules || !$id)
            return true;

        return $this->doValidate($request, $rules, $messages, $customAttributes, $throwable);
    }
}
