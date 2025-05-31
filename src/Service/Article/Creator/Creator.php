<?php

namespace App\Service\Article\Creator;

use App\Entity\Article;
use App\Entity\User;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Exception\ForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class Creator
{
    public function __construct(
        private readonly Security               $security,
        private readonly FormHandler            $formHandler,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function create(Request $request): int
    {
        if (!$this->security->isGranted(ArticleVoter::CREATE, new Article())) {
            throw new ForbiddenException('You are not allowed to create an article');
        }

        $newArticle = $this->formHandler->handle($request);

        /** @var User $user */
        $user = $this->security->getUser();

        $user->addArticle($newArticle);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $newArticle->getId();
    }
}