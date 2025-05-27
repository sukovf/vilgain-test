<?php

namespace App\Tests\Fixtures\Controller\User;

use App\Entity\User;
use App\Security\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Get extends Fixture
{
    public const USER_AUTHOR_EMAIL = 'author@bar.com';
    public const USER_AUTHOR_NAME = 'Joe Author';
    public const USER_AUTHOR_ROLE = UserRole::AUTHOR;
    public const USER_AUTHOR_REFERENCE = 'user.author';

    public const USER_READER_EMAIL = 'reader@bar.com';
    public const USER_READER_NAME = 'Joe Reader';
    public const USER_READER_ROLE = UserRole::READER;

    public function load(ObjectManager $manager): void
    {
        $userAuthor = (new User())
            ->setEmail(self::USER_AUTHOR_EMAIL)
            ->setPassword('abcd')
            ->setName(self::USER_AUTHOR_NAME)
            ->setRole(self::USER_AUTHOR_ROLE);

        $userReader = (new User())
            ->setEmail(self::USER_READER_EMAIL)
            ->setPassword('abcd')
            ->setName(self::USER_READER_NAME)
            ->setRole(self::USER_READER_ROLE);

        $manager->persist($userAuthor);
        $manager->persist($userReader);
        $manager->flush();

        $this->addReference(self::USER_AUTHOR_REFERENCE, $userAuthor);
    }
}