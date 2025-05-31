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
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractFOSRestController
{
    public function __construct(private readonly Facade $userFacade) {}

    #[Post('/auth/login')]
    #[OA\Post(
        description: 'User login',
        summary: 'User login',
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    'username'  => new OA\Property(property: 'username', type: 'string'),
                    'password'  => new OA\Property(property: 'password', type: 'string')
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: SymfonyResponse::HTTP_OK, description: 'Successful login'),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized')
        ]
    )]
    public function login(): void {}

    #[Post('/auth/register')]
    #[OA\Post(
        description: 'Register a new user',
        summary: 'Register a new user',
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'name', 'role'],
                properties: [
                    'email'     => new OA\Property(property: 'email', type: 'string', format: 'email'),
                    'password'  => new OA\Property(property: 'password', type: 'string', minLength: 6),
                    'name'      => new OA\Property(property: 'name', type: 'string'),
                    'role'      => new OA\Property(property: 'role', type: 'string', enum: UserRole::class, example: 'ROLE_ADMIN')
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_CREATED,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            'newUserId' => new OA\Property(property: 'newUserId', type: 'integer')
                        ]
                    )
                )),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_CONFLICT, description: 'User already exists')
        ]
    )]
    public function register(Request $request): Response
    {
        $newUserId = $this->userFacade->create($request);

        return new Response([
            'newUserId' => $newUserId
        ], SymfonyResponse::HTTP_CREATED, 'User registered successfully');
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Get('/users')]
    #[OA\Get(
        description: 'Retrieve a list of all users',
        summary: 'Get all users',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_OK,
                description: 'List of users retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            'id'    => new OA\Property(property: 'id', type: 'integer'),
                            'email' => new OA\Property(property: 'email', type: 'string'),
                            'name'  => new OA\Property(property: 'name', type: 'string'),
                            'role'  => new OA\Property(property: 'role', type: 'string')
                        ]
                    )
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden')
        ]
    )]
    public function getUsers(): Response
    {
        return new Response($this->userFacade->getAll(), message: 'List of users retrieved successfully');
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Get('/users/{id}')]
    #[OA\Get(
        description: 'Retrieve a specific user by their ID',
        summary: 'Get user by ID',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_OK,
                description: 'User retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        'id'    => new OA\Property(property: 'id', type: 'integer'),
                        'email' => new OA\Property(property: 'email', type: 'string'),
                        'name'  => new OA\Property(property: 'name', type: 'string'),
                        'role'  => new OA\Property(property: 'role', type: 'string')
                    ]
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'User not found')
        ]
    )]
    public function getOneUser(int $id): Response
    {
        return new Response($this->userFacade->getOne($id), message: 'User retrieved successfully');
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Post('/users')]
    #[OA\Post(
        description: 'Create a new user account',
        summary: 'Create a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'name', 'role'],
                properties: [
                    'email'     => new OA\Property(property: 'email', type: 'string', format: 'email'),
                    'password'  => new OA\Property(property: 'password', type: 'string', minLength: 6),
                    'name'      => new OA\Property(property: 'name', type: 'string'),
                    'role'      => new OA\Property(property: 'role', type: 'string', enum: UserRole::class, example: 'ROLE_ADMIN')
                ]
            )
        ),
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_CREATED,
                description: 'User created successfully',
                content: new OA\JsonContent(
                    properties: [
                        'newUserId' => new OA\Property(property: 'newUserId', type: 'integer')
                    ]
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_CONFLICT, description: 'User already exists')
        ]
    )]
    public function createUser(Request $request): Response
    {
        $newUserId = $this->userFacade->create($request);

        return new Response([
            'newUserId' => $newUserId
        ], SymfonyResponse::HTTP_CREATED, 'User created successfully');
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Put('/users/{id}')]
    #[OA\Put(
        description: 'Update an existing user',
        summary: 'Update user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                    'name'  => new OA\Property(property: 'name', type: 'string'),
                    'role'  => new OA\Property(property: 'role', type: 'string', enum: UserRole::class, example: 'ROLE_ADMIN')
                ]
            )
        ),
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: SymfonyResponse::HTTP_OK, description: 'User updated successfully'),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'User not found'),
        ]
    )]
    public function updateUser(int $id, Request $request): Response
    {
        $this->userFacade->update($id, $request);

        return new Response(message: 'User updated successfully');
    }

    #[IsGranted(UserRole::ADMIN->value)]
    #[Delete('/users/{id}')]
    #[OA\Delete(
        description: 'Delete an existing user',
        summary: 'Delete user',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: SymfonyResponse::HTTP_OK, description: 'User deleted successfully'),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'User not found'),
            new OA\Response(response: SymfonyResponse::HTTP_CONFLICT, description: 'Cannot delete user with articles')
        ]
    )]
    public function deleteUser(int $id): Response
    {
        $this->userFacade->delete($id);

        return new Response(message: 'User deleted successfully');
    }
}