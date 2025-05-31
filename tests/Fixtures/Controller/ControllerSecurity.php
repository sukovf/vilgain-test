<?php

namespace App\Tests\Fixtures\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Security\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ControllerSecurity extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'user.admin@bar.com';
    public const AUTHOR1_USER_REFERENCE = 'user.author1@bar.com';
    public const AUTHOR2_USER_REFERENCE = 'user.author2@bar.com';
    public const READER_USER_REFERENCE = 'user.reader@bar.com';

    public const ARTICLE1_REFERENCE = 'article.1';
    public const ARTICLE2_REFERENCE = 'article.2';

    public function load(ObjectManager $manager): void
    {
        // Users
        $userAdmin = (new User())
            ->setEmail('admin@bar.com')
            ->setPassword('abcd')
            ->setName('John Admin')
            ->setRole(UserRole::ADMIN);

        $userAuthor1 = (new User())
            ->setEmail('author1@bar.com')
            ->setPassword('abcd')
            ->setName('John Author1')
            ->setRole(UserRole::AUTHOR);

        $userAuthor2 = (new User())
            ->setEmail('author2@bar.com')
            ->setPassword('abcd')
            ->setName('John Author2')
            ->setRole(UserRole::AUTHOR);

        $userReader = (new User())
            ->setEmail('reader@bar.com')
            ->setPassword('abcd')
            ->setName('John Reader')
            ->setRole(UserRole::READER);

        // Articles
        $article1 = (new Article())
            ->setTitle('Foo Bar1')
            ->setContent('Blah blah blah');

        $article2 = (new Article())
            ->setTitle('Foo Bar2')
            ->setContent('Blah blah blah');

        $userAuthor1->addArticle($article1);
        $userAuthor2->addArticle($article2);

        $manager->persist($userAdmin);
        $manager->persist($userAuthor1);
        $manager->persist($userAuthor2);
        $manager->persist($userReader);
        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
        $this->addReference(self::AUTHOR1_USER_REFERENCE, $userAuthor1);
        $this->addReference(self::AUTHOR2_USER_REFERENCE, $userAuthor2);
        $this->addReference(self::READER_USER_REFERENCE, $userReader);

        $this->addReference(self::ARTICLE1_REFERENCE, $article1);
        $this->addReference(self::ARTICLE2_REFERENCE, $article2);
    }
}