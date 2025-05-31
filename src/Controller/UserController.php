<?php

namespace App\Controller;

use App\Api\Response;
use App\Service\User\Facade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class UserController extends AbstractFOSRestController
{
    public function __construct(private readonly Facade $userFacade) {}

    #[Post('/auth/register')]
    public function register(Request $request): Response
    {
        $newUserId = $this->userFacade->create($request);

        return new Response([
            'newUserId' => $newUserId
        ], SymfonyResponse::HTTP_CREATED);
    }
}