<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Exception\UserNotFoundException;

class Getter
{
    public function __construct(private readonly UserRepository $userRepository) {}

    /**
     * @return User[]
     */
    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function getOne(int $id): User
    {
        if (!$user = $this->userRepository->find($id)) {
            throw new UserNotFoundException('User not found');
        }

        return $user;
    }
}