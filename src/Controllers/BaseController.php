<?php
/**
 * @Author yaangvu
 * @Date   Sep 02, 2022
 */

namespace YaangVu\LaravelBase\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use YaangVu\LaravelBase\Services\BaseService;

abstract class BaseController extends Controller
{
    public BaseService $service;

    function __construct()
    {
        $this->initService();
    }

    abstract protected function initService();

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return \response()->json($this->service->getAll());
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
        return \response()->json($this->service->add($request))->setStatusCode(Response::HTTP_CREATED);
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
        return \response()->json($this->service->get($id));
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
        return \response()->json($this->service->update($id, $request));
    }

    /**
     * Put Update the specified resource in storage.
     *
     * @param         $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function putUpdate($id, Request $request): JsonResponse
    {
        return \response()->json($this->service->putUpdate($id, $request));
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
        return \response()->json($this->service->delete($id));
    }

    /**
     * Remove the specified resource from storage by uuid
     *
     * @param $uuid
     *
     * @return JsonResponse
     */
    public function deleteByUuid($uuid): JsonResponse
    {
        return \response()->json($this->service->deleteByUuid($uuid));
    }

    /**
     * Display the specified resource.
     *
     * @param $uuid
     *
     * @return JsonResponse
     */
    public function showByUuid($uuid): JsonResponse
    {
        return \response()->json($this->service->getByUuid($uuid));
    }

    /**
     * Remove the multiple specified resource from storage by ids
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteByIds(Request $request): JsonResponse
    {
        return \response()->json($this->service->deleteByIds($request));
    }

    /**
     * Remove the multiple specified resource from storage by uuids
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteByUuids(Request $request): JsonResponse
    {
        return \response()->json($this->service->deleteByUuids($request));
    }
}
