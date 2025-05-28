<?php

namespace App\Service\Article\Creator;

use App\Form\Article\CreateArticleFormType;
use App\Service\Article\Factory;
use App\Service\Article\Creator\Exception\LogicException;
use App\Service\Article\Exception\CreateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{
    public function __construct(
        private readonly Factory              $articleFactory,
        private readonly FormFactoryInterface $formFactory,
        private readonly ErrorSerializer      $formErrorSerializer,
        private readonly HandlerOutputFactory $handlerOutputFactory
    ) {}

    public function handle(Request $request): HandlerOutput
    {
        $newArticle = $this->articleFactory->create();

        $form = $this->formFactory->create(CreateArticleFormType::class, $newArticle);
        $form->submit($request->toArray());

        if (!$form->isValid()) {
            throw new CreateArticleBadRequestException($this->formErrorSerializer->serialize($form));
        }

        $authorUserId = $form->get('author_id')->getData();
        if (!is_int($authorUserId)) {
            throw new LogicException('The author_id must be an integer');
        }

        return $this->handlerOutputFactory->create($newArticle, $authorUserId);
    }
}