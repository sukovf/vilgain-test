<?php

namespace App\Service\Article;

use App\Service\Article\Creator\Creator;
use App\Service\Article\Updater\Updater;
use Symfony\Component\HttpFoundation\Request;

class Facade
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Updater $updater,
        private readonly Deleter $deleter
    ) {}

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