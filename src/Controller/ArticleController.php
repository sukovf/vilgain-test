<?php

namespace App\Controller;

use App\Api\Response;
use App\Service\Article\Facade;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ArticleController extends AbstractFOSRestController
{
    public function __construct(private readonly Facade $articleFacade) {}

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
}