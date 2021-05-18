<?php

namespace YaangVu\LaravelBase\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use YaangVu\LaravelBase\Services\impl\BaseService;

class BaseController extends Controller
{
    public BaseService $service;

    function __construct()
    {
        // Init $service
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->service->getAll());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json($this->service->add($request))->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->json($this->service->get($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param         $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        return response()->json($this->service->update($id, $request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return response()->json($this->service->delete($id));
    }

    /**
     * Remove the specified resource from storage by code
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteByCode($code): JsonResponse
    {
        return response()->json($this->service->deleteByCode($code));
    }

    /**
     * Display the specified resource.
     *
     * @param $code
     *
     * @return JsonResponse
     */
    public function showByCode($code): JsonResponse
    {
        return response()->json($this->service->getByCode($code));
    }

    /**
     * Remove multiple the specified resource from storage by ids
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteByIds(Request $request): JsonResponse
    {
        return response()->json($this->service->deleteByIds($request));
    }

    /**
     * Remove multiple the specified resource from storage by codes
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteByCodes(Request $request): JsonResponse
    {
        return response()->json($this->service->deleteByCodes($request));
    }
}
