<?php

namespace App\Service\User;

use App\Service\User\Creator\Creator;
use Symfony\Component\HttpFoundation\Request;

class Facade
{
    public function __construct(private readonly Creator $creator) {}

    public function create(Request $request): int
    {
        return $this->creator->create($request);
    }
}