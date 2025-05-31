<?php

namespace App\Service\Article\Creator;

use App\Entity\Article;
use App\Repository\UserRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Exception\ForbiddenException;
use App\Service\User\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class Creator
{
    public function __construct(
        private readonly Security               $security,
        private readonly FormHandler            $formHandler,
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function create(Request $request): int
    {
        if (!$this->security->isGranted(ArticleVoter::CREATE, new Article())) {
            throw new ForbiddenException('You are not allowed to create an article');
        }

        $handlerOutput = $this->formHandler->handle($request);

        $newArticle = $handlerOutput->getArticle();

        if (!($authorUser = $this->userRepository->find($handlerOutput->getAuthorUserId()))) {
            throw new UserNotFoundException('User not found');
        }

        $authorUser->addArticle($newArticle);

        $this->entityManager->persist($authorUser);
        $this->entityManager->flush();

        return $newArticle->getId();
    }
}