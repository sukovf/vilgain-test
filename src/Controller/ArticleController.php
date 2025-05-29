<?php

namespace App\Controller;

use App\Api\Response;
use App\Service\Article\Facade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ArticleController extends AbstractFOSRestController
{
    public function __construct(private readonly Facade $articleFacade) {}

    #[Get('/articles')]
    public function getArticles(): Response
    {
        return new Response($this->articleFacade->getAll());
    }

    #[Get('/articles/{id}')]
    public function getOneArticle(int $id): Response
    {
        return new Response($this->articleFacade->getOne($id));
    }

    #[Post('/articles')]
    public function createArticle(Request $request): Response
    {
        $newArticleId = $this->articleFacade->create($request);

        return new Response([
            'newArticleId' => $newArticleId
        ], SymfonyResponse::HTTP_CREATED);
    }

    #[Put('/articles/{id}')]
    public function updateArticle(int $id, Request $request): Response
    {
        $this->articleFacade->update($id, $request);

        return new Response();
    }

    #[Delete('/articles/{id}')]
    public function deleteArticle(int $id): Response
    {
        $this->articleFacade->delete($id);

        return new Response();
    }
}