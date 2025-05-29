<?php

namespace App\Service\Article;

use App\Entity\Article;
use App\Service\Article\Creator\Creator;
use App\Service\Article\Updater\Updater;
use Symfony\Component\HttpFoundation\Request;

class Facade
{
    public function __construct(
        private readonly Getter  $getter,
        private readonly Creator $creator,
        private readonly Updater $updater,
        private readonly Deleter $deleter
    ) {}

    /**
     * @return Article[]
     */
    public function getAll(): array
    {
        return $this->getter->getAll();
    }

    public function getOne(int $id): Article
    {
        return $this->getter->getOne($id);
    }

    public function create(Request $request): int
    {
        return $this->creator->create($request);
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