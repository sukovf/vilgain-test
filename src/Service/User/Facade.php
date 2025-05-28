<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\User\Creator\Creator;
use App\Service\User\Updater\Updater;
use Symfony\Component\HttpFoundation\Request;

class Facade
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Getter  $getter,
        private readonly Updater $updater,
        private readonly Deleter $deleter
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

    public function update(int $id, Request $request): void
    {
        $this->updater->update($id, $request);
    }

    public function delete(int $id): void
    {
        $this->deleter->delete($id);
    }
}