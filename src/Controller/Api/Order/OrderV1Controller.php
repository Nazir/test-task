<?php

namespace App\Controller\Api\Order;

use App\Controller\Api\ApiControllerDef;
use App\Controller\BaseController;
use App\Model\Api\ApiResponse;
use App\Modules\Order\Dto\OrderDto;
use App\Modules\Order\Model\OrderList;
use App\Modules\Order\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/order', name: 'api_v1_order')]
class OrderV1Controller extends BaseController
{
    public function __construct(
        private readonly OrderService $service,
    ) {
    }

    /**
     * List
     */
    #[Route(
        path: ApiControllerDef::ROUTE_PATH_LIST,
        name: ApiControllerDef::ROUTE_NAME_LIST,
        methods: ApiControllerDef::ROUTE_METHODS_LIST,
    )]
    public function list(#[MapQueryString()] OrderList $list): ApiResponse
    {
        return new ApiResponse(data: $this->service->list(list: $list)());
    }

    #[Route(path: '', name: ApiControllerDef::ROUTE_NAME_CREATE, methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload()] OrderDto $dto): ApiResponse
    {
        $obj = $this->service->create(dto: $dto);

        return new ApiResponse(data: $obj);
    }

    /**
     * Read
     */
    #[Route(
        path: ApiControllerDef::ROUTE_PATH_READ,
        name: ApiControllerDef::ROUTE_NAME_READ,
        requirements: ApiControllerDef::ROUTE_REQUIREMENTS_ID,
        methods: ApiControllerDef::ROUTE_METHODS_READ,
    )]
    public function read(int $id): ApiResponse
    {
        return new ApiResponse(data: $this->service->read($id));
    }

    #[Route(
        path: ApiControllerDef::ROUTE_PATH_UPDATE,
        name: ApiControllerDef::ROUTE_NAME_UPDATE,
        requirements: ApiControllerDef::ROUTE_REQUIREMENTS_ID,
        methods: ApiControllerDef::ROUTE_METHODS_UPDATE,
    )]
    public function update(int $id, #[MapRequestPayload()] OrderDto $dto): ApiResponse
    {
        $obj = $this->service->update(objOrObjId: $id, dto: $dto);

        return new ApiResponse(data: $obj);
    }

    #[Route(
        path: ApiControllerDef::ROUTE_PATH_DELETE,
        name: ApiControllerDef::ROUTE_NAME_DELETE,
        requirements: ApiControllerDef::ROUTE_REQUIREMENTS_ID,
        methods: ApiControllerDef::ROUTE_METHODS_DELETE,
    )]
    public function delete(int $id): ApiResponse
    {
        $this->service->delete(objOrObjId: $id);

        return new ApiResponse(status: ApiResponse::HTTP_NO_CONTENT);
    }
}
