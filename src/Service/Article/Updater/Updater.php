<?php

namespace App\Service\Article\Updater;

use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Exception\ArticleNotFoundException;
use App\Service\Article\Exception\ForbiddenException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class Updater
{
    public function __construct(
        private readonly Security               $security,
        private readonly FormHandler            $formHandler,
        private readonly ArticleRepository      $articleRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function update(int $id, Request $request): void
    {
        if (!($article = $this->articleRepository->find($id))) {
            throw new ArticleNotFoundException('Article not found');
        }

        if (!$this->security->isGranted(ArticleVoter::UPDATE, $article)) {
            throw new ForbiddenException('You are not allowed to update this article');
        }

        $article = $this->formHandler->handle($article, $request);

        $article->setUpdatedAt(new DateTime());

        $this->entityManager->persist($article->getAuthor());
        $this->entityManager->flush();
    }
}