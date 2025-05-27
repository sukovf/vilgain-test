<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\User\Creator\Creator;
use Symfony\Component\HttpFoundation\Request;

class Facade
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Getter  $getter
    ) {}

    public function create(Request $request): int
    {
        return $this->creator->create($request);
    }

    /**
     * @return User[]
     */
    public function getAll(): array
    {
        return $this->getter->getAll();
    }

    public function getOne(int $id): User
    {
        return $this->getter->getOne($id);
    }
}