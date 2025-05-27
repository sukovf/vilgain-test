<?php

namespace App\Service\User\Creator;

use App\Repository\UserRepository;
use App\Service\User\Exception\UserExistsException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Creator
{
    public function __construct(
        private readonly FormHandler                 $formHandler,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager
    ) {}

    public function create(Request $request): int
    {
        $handlerOutput = $this->formHandler->handle($request);

        $newUser = $handlerOutput->getUser();

        if ($this->userRepository->findOneBy(['email' => $newUser->getEmail()])) {
            throw new UserExistsException(sprintf('User with email "%s" already exists', $newUser->getEmail()));
        }

        $hashedPassword = $this->passwordHasher->hashPassword($newUser, $handlerOutput->getPlainPassword());

        $newUser->setPassword($hashedPassword);

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return $newUser->getId();
    }
}