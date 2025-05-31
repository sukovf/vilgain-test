<?php

namespace App\Controller;

use App\Api\Response;
use App\Security\UserRole;
use App\Service\User\Facade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[IsGranted(UserRole::ADMIN->value)]
    #[Get('/users')]
    public function getUsers(): Response
    {
        return new Response($this->userFacade->getAll());
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Get('/users/{id}')]
    public function getOneUser(int $id): Response
    {
        return new Response($this->userFacade->getOne($id));
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Post('/users')]
    public function createUser(Request $request): Response
    {
        $newUserId = $this->userFacade->create($request);

        return new Response([
            'newUserId' => $newUserId
        ], SymfonyResponse::HTTP_CREATED);
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Put('/users/{id}')]
    public function updateUser(int $id, Request $request): Response
    {
        $this->userFacade->update($id, $request);

        return new Response();
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Delete('/users/{id}')]
    public function deleteUser(int $id): Response
    {
        $this->userFacade->delete($id);

        return new Response();
    }
}