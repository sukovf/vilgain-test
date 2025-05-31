<?php

namespace App\Service\Article;

use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Exception\ArticleNotFoundException;
use App\Service\Article\Exception\ForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Deleter
{
    public function __construct(
        private readonly Security               $security,
        private readonly ArticleRepository      $articleRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function delete(int $id): void
    {
        if (!($article = $this->articleRepository->find($id))) {
            throw new ArticleNotFoundException('Article not found');
        }

        if (!$this->security->isGranted(ArticleVoter::DELETE, $article)) {
            throw new ForbiddenException('You are not allowed to delete this article');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}