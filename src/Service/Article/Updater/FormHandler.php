<?php

namespace App\Service\Article\Updater;

use App\Entity\Article;
use App\Form\Article\UpdateArticleFormType;
use App\Service\Article\Exception\UpdateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ErrorSerializer      $formErrorSerializer
    ) {}

    public function handle(Article $article, Request $request): Article
    {
        $form = $this->formFactory->create(UpdateArticleFormType::class, $article);
        $form->submit($request->toArray(), false);

        if (!$form->isValid()) {
            throw new UpdateArticleBadRequestException($this->formErrorSerializer->serialize($form));
        }

        return $article;
    }
}