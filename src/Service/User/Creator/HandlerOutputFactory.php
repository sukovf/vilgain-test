<?php

namespace App\Service\User\Creator;

use App\Entity\User;

class HandlerOutputFactory
{
    public function create(User $user, string $plainTextPassword): HandlerOutput
    {
        return new HandlerOutput($user, $plainTextPassword);
    }
}