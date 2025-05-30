<?php

namespace App\Service\User;

use App\Repository\UserRepository;
use App\Service\User\Exception\UserHasArticlesException;
use App\Service\User\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class Deleter
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function delete(int $id): void
    {
        if (!($user = $this->userRepository->find($id))) {
            throw new UserNotFoundException('User not found');
        }

        if ($user->getArticles()->count() > 0) {
            throw new UserHasArticlesException('Cannot delete user with articles');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}