<?php

namespace App\Controller\Api\References;

use App\Controller\Api\BaseApiController;
use App\Controller\Api\ApiControllerDef as Def;
use App\Model\Api\ApiResponse;
use App\Service\References\Model\ReferencesList;
use App\Service\References\ReferencesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/references', name: 'api_v1_references')]
class ReferencesV1Controller extends BaseApiController
{
    private const ROUTE_PATH_REF_NAME = '/{refName}';

    public function __construct(
        private readonly ReferencesService $service,
    ) {
    }

    #[Route(
        path: '/',
        name: Def::ROUTE_NAME_INDEX,
        methods: Def::ROUTE_METHODS_INDEX,
    )]
    public function index(): ApiResponse
    {
        return new ApiResponse(data: $this->service->listAll());
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME,
        name: Def::ROUTE_NAME_LIST,
        methods: Def::ROUTE_METHODS_LIST,
    )]
    public function list(string $refName, #[MapQueryString()] ReferencesList $list = null): ApiResponse
    {
        return new ApiResponse(data: $this->service->list(refName: $refName, list: $list));
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME . Def::ROUTE_PATH_READ,
        name: Def::ROUTE_NAME_READ,
        methods: Def::ROUTE_METHODS_READ,
    )]
    public function read(string $refName, int $id): ApiResponse
    {
        $obj = $this->service->read(refName: $refName, objOrObjId: $id);

        return new ApiResponse(data: $obj);
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME,
        name: Def::ROUTE_NAME_CREATE,
        methods: Def::ROUTE_METHODS_CREATE,
    )]
    public function create(string $refName, Request $request): ApiResponse
    {
        $obj = $this->service->create(refName: $refName, data: $request->getContent());

        return new ApiResponse(data: $obj);
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME . Def::ROUTE_PATH_UPDATE,
        name: Def::ROUTE_NAME_UPDATE,
        methods: Def::ROUTE_METHODS_UPDATE,
    )]
    public function update(string $refName, int $id, Request $request): ApiResponse
    {
        $obj = $this->service->update(refName: $refName, objOrObjId: $id, data: $request->getContent());

        return new ApiResponse(data: $obj);
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME . Def::ROUTE_PATH_DELETE,
        name: Def::ROUTE_NAME_DELETE,
        methods: Def::ROUTE_METHODS_DELETE,
    )]
    public function delete(string $refName, int $id): ApiResponse
    {
        $this->service->delete(refName: $refName, objOrObjId: $id);

        return new ApiResponse(status: ApiResponse::HTTP_NO_CONTENT);
    }

    #[Route(
        path: self::ROUTE_PATH_REF_NAME . Def::ROUTE_PATH_PATCH . '/restore',
        name: Def::ROUTE_PATH_PATCH . '_restore',
        requirements: Def::ROUTE_REQUIREMENTS_ID,
        methods: Def::ROUTE_METHODS_PATCH,
    )]
    public function restore(string $refName, int $id): ApiResponse
    {
        $this->service->restore(refName: $refName, objOrObjId: $id);

        return new ApiResponse(status: ApiResponse::HTTP_NO_CONTENT);
    }
}
