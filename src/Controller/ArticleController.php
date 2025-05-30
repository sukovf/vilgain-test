<?php

namespace App\Controller;

use App\Api\Response;
use App\Service\Article\Facade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ArticleController extends AbstractFOSRestController
{
    public function __construct(private readonly Facade $articleFacade) {}

    #[Get('/articles')]
    #[OA\Get(
        description: 'Retrieve a list of all articles',
        summary: 'Get all articles',
        tags: ['Articles'],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_OK,
                description: 'List of articles retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            'id'        => new OA\Property(property: 'id', type: 'integer'),
                            'title'     => new OA\Property(property: 'title', type: 'string'),
                            'content'   => new OA\Property(property: 'content', type: 'string'),
                            'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'Y-m-d H:i:s', example: '20025-05-11 15:20:10'),
                            'updatedAt' => new OA\Property(property: 'updatedAt', type: 'string', format: 'Y-m-d H:i:s', example: '20025-05-11 15:20:10', nullable: true)
                        ]
                    )
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized')
        ]
    )]
    public function getArticles(): Response
    {
        return new Response($this->articleFacade->getAll());
    }

    #[Get('/articles/{id}')]
    #[OA\Get(
        description: 'Retrieve a specific article by its ID',
        summary: 'Get article by ID',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Article ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_OK,
                description: 'Article retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        'id'        => new OA\Property(property: 'id', type: 'integer'),
                        'title'     => new OA\Property(property: 'title', type: 'string'),
                        'content'   => new OA\Property(property: 'content', type: 'string'),
                        'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'Y-m-d H:i:s', example: '20025-05-11 15:20:10'),
                        'updatedAt' => new OA\Property(property: 'updatedAt', type: 'string', format: 'Y-m-d H:i:s', example: '20025-05-11 15:20:10', nullable: true)
                    ]
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'Article not found')
        ]
    )]
    public function getOneArticle(int $id): Response
    {
        return new Response($this->articleFacade->getOne($id));
    }

    #[Post('/articles')]
    #[OA\Post(
        description: 'Create a new article',
        summary: 'Create a new article',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content', 'author_id'],
                properties: [
                    'title'     => new OA\Property(property: 'title', type: 'string'),
                    'content'   => new OA\Property(property: 'content', type: 'string'),
                    'author_id' => new OA\Property(property: 'author_id', type: 'integer')
                ]
            )
        ),
        tags: ['Articles'],
        responses: [
            new OA\Response(
                response: SymfonyResponse::HTTP_CREATED,
                description: 'Article created successfully',
                content: new OA\JsonContent(
                    properties: [
                        'newArticleId' => new OA\Property(property: 'newArticleId', type: 'integer')
                    ]
                )
            ),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'Author user not found')
        ]
    )]
    public function createArticle(Request $request): Response
    {
        $newArticleId = $this->articleFacade->create($request);

        return new Response([
            'newArticleId' => $newArticleId
        ], SymfonyResponse::HTTP_CREATED);
    }

    #[Put('/articles/{id}')]
    #[OA\Put(
        description: 'Update an existing article',
        summary: 'Update article',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    'title'   => new OA\Property(property: 'title', type: 'string'),
                    'content' => new OA\Property(property: 'content', type: 'string')
                ]
            )
        ),
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Article ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: SymfonyResponse::HTTP_OK, description: 'Article updated successfully'),
            new OA\Response(response: SymfonyResponse::HTTP_BAD_REQUEST, description: 'Bad request'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'Article not found')
        ]
    )]
    public function updateArticle(int $id, Request $request): Response
    {
        $this->articleFacade->update($id, $request);

        return new Response();
    }

    #[Delete('/articles/{id}')]
    #[OA\Delete(
        description: 'Delete an existing article',
        summary: 'Delete article',
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Article ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: SymfonyResponse::HTTP_OK, description: 'Article deleted successfully'),
            new OA\Response(response: SymfonyResponse::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: SymfonyResponse::HTTP_NOT_FOUND, description: 'Article not found')
        ]
    )]
    public function deleteArticle(int $id): Response
    {
        $this->articleFacade->delete($id);

        return new Response();
    }
}