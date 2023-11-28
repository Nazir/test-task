<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    #[Route(path: '/', name: 'app_index')]
    public function index(): Response
    {
        return new Response(status: Response::HTTP_NOT_FOUND);
    }
}
