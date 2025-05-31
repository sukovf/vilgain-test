<?php

namespace App\Service\Article\Creator;

use App\Entity\Article;
use App\Form\Article\CreateArticleFormType;
use App\Service\Article\Factory;
use App\Service\Article\Exception\CreateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{
    public function __construct(
        private readonly Factory              $articleFactory,
        private readonly FormFactoryInterface $formFactory,
        private readonly ErrorSerializer      $formErrorSerializer
    ) {}

    public function handle(Request $request): Article
    {
        $newArticle = $this->articleFactory->create();

        $form = $this->formFactory->create(CreateArticleFormType::class, $newArticle);
        $form->submit($request->toArray());

        if (!$form->isValid()) {
            throw new CreateArticleBadRequestException($this->formErrorSerializer->serialize($form));
        }

        return $newArticle;
    }
}