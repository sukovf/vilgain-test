<?php

namespace App\Security;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case AUTHOR = 'ROLE_AUTHOR';
    case READER = 'ROLE_READER';
}
