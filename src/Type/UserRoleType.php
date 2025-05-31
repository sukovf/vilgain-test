<?php

namespace App\Type;

use App\Security\UserRole;

class UserRoleType extends EnumType
{
    public const NAME = 'user_role';

    /**
     * @inheritDoc
     */
    protected function getEnumClass(): string
    {
        return UserRole::class;
    }
}