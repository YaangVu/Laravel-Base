<?php
/**
 * @Author yaangvu
 * @Date   Sep 02, 2022
 */

namespace YaangVu\LaravelBase\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use YaangVu\LaravelBase\Base\Contract\Service;

class BaseController extends Controller
{
    /**
     * @var Service $service
     */
    protected $service;

    /**
     * @param Model  $model
     * @param string $serviceName
     */
    public function __construct(private readonly Model $model, string $serviceName)
    {
        $this->service = new $serviceName($this->model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return \response()->json($this->service->get());
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
        return \response()->json($this->service->find($id));
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
        return \response()->json($this->service->findByUuid($uuid));
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
