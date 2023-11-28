<?php

namespace App\Controller\Api\Customer;

use App\Controller\Api\ApiControllerDef as Def;
use App\Controller\BaseController;
use App\Model\Api\ApiResponse;
use App\Modules\Customer\Dto\CustomerDto;
use App\Modules\Customer\Model\CustomerList;
use App\Modules\Customer\Service\CustomerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/customer', name: 'api_v1_customer')]
class CustomerV1Controller extends BaseController
{
    public function __construct(
        private readonly CustomerService $service,
    ) {
    }

    /**
     * List
     */
    #[Route(
        path: Def::ROUTE_PATH_LIST,
        name: Def::ROUTE_NAME_LIST,
        methods: Def::ROUTE_METHODS_LIST,
    )]
    public function list(#[MapQueryString()] CustomerList $list): ApiResponse
    {
        return new ApiResponse(data: $this->service->list(list: $list)());
    }

    #[Route(path: '', name: Def::ROUTE_NAME_CREATE, methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload()] CustomerDto $dto): ApiResponse
    {
        $obj = $this->service->create(dto: $dto);

        return new ApiResponse(data: $obj);
    }

    /**
     * Read
     */
    #[Route(
        path: Def::ROUTE_PATH_READ,
        name: Def::ROUTE_NAME_READ,
        requirements: Def::ROUTE_REQUIREMENTS_ID,
        methods: Def::ROUTE_METHODS_READ,
    )]
    public function read(int $id): ApiResponse
    {
        return new ApiResponse(data: $this->service->read($id));
    }

    #[Route(
        path: Def::ROUTE_PATH_UPDATE,
        name: Def::ROUTE_NAME_UPDATE,
        requirements: Def::ROUTE_REQUIREMENTS_ID,
        methods: Def::ROUTE_METHODS_UPDATE,
    )]
    public function update(int $id, #[MapRequestPayload()] CustomerDto $dto): ApiResponse
    {
        $obj = $this->service->update(objOrObjId: $id, dto: $dto);

        return new ApiResponse(data: $obj);
    }

    #[Route(
        path: Def::ROUTE_PATH_DELETE,
        name: Def::ROUTE_NAME_DELETE,
        requirements: Def::ROUTE_REQUIREMENTS_ID,
        methods: Def::ROUTE_METHODS_DELETE,
    )]
    public function delete(int $id): ApiResponse
    {
        $this->service->delete(objOrObjId: $id);

        return new ApiResponse(status: ApiResponse::HTTP_NO_CONTENT);
    }

    #[Route(
        path: Def::ROUTE_PATH_PATCH . '/restore',
        name: Def::ROUTE_PATH_PATCH . '_restore',
        requirements: Def::ROUTE_REQUIREMENTS_ID,
        methods: Def::ROUTE_METHODS_PATCH,
    )]
    public function restore(int $id): ApiResponse
    {
        $this->service->restore(objOrObjId: $id);

        return new ApiResponse(status: ApiResponse::HTTP_NO_CONTENT);
    }
}
