<?php

namespace App\Service\User\Creator;

use App\Entity\User;

class HandlerOutput
{
    public function __construct(
        private readonly User   $user,
        private readonly string $plainPassword
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}