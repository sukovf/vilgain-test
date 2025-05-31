<?php

namespace App\Service\User;

use App\Entity\User;

class Factory
{
    public function create(): User
    {
        return new User();
    }
}