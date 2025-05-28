<?php

namespace App\Service\User\Updater;

use App\Repository\UserRepository;
use App\Service\User\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Updater
{
    public function __construct(
        private readonly FormHandler            $formHandler,
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function update(int $id, Request $request): void
    {
        if (!($user = $this->userRepository->find($id))) {
            throw new UserNotFoundException('User not found');
        }

        $user = $this->formHandler->handle($user, $request);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}