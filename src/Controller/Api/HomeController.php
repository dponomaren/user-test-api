<?php

namespace App\Controller\Api;

use App\Model\Response\Api\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends CoreRestApiController
{
    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     */
    public function homeAction(Request $request): ApiResponse
    {
        return $this->serialize('User micro service', ApiResponse::HTTP_OK, ['get']);
    }
}