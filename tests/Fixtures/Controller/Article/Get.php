<?php

namespace App\Tests\Fixtures\Controller\Article;

use App\Entity\Article;
use App\Entity\User;
use App\Security\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Get extends Fixture
{
    public const ARTICLE1_TITLE = 'Foo Bar1';
    public const ARTICLE1_CONTENT = 'Blah blah blah';

    public const ARTICLE2_TITLE = 'Foo Bar2';
    public const ARTICLE2_CONTENT = 'Blah blah blah';

    public const AUTHOR1_USER_REFERENCE = 'user.author1@bar.com';
    public const AUTHOR2_USER_REFERENCE = 'user.author2@bar.com';

    public const ARTICLE1_REFERENCE = 'article.1';
    public const ARTICLE2_REFERENCE = 'article.2';

    public function load(ObjectManager $manager): void
    {
        // Users
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

        // Articles
        $article1 = (new Article())
            ->setTitle(self::ARTICLE1_TITLE)
            ->setContent(self::ARTICLE1_CONTENT);

        $article2 = (new Article())
            ->setTitle(self::ARTICLE2_TITLE)
            ->setContent(self::ARTICLE2_CONTENT);

        $userAuthor1->addArticle($article1);
        $userAuthor2->addArticle($article2);

        $manager->persist($userAuthor1);
        $manager->persist($userAuthor2);
        $manager->flush();

        $this->addReference(self::AUTHOR1_USER_REFERENCE, $userAuthor1);
        $this->addReference(self::AUTHOR2_USER_REFERENCE, $userAuthor2);

        $this->addReference(self::ARTICLE1_REFERENCE, $article1);
        $this->addReference(self::ARTICLE2_REFERENCE, $article2);
    }
}