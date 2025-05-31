<?php

namespace App\Tests\Integration\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Security\UserRole;
use App\Tests\BaseWebTestCase;
use App\Tests\Fixtures\Controller\Article\Get as ArticleGetFixture;
use App\Tests\Fixtures\Controller\User\Get as UserGetFixture;
use App\Tests\Integration\Controller\JsonSchema\Article\GetAll;
use App\Tests\Integration\Controller\JsonSchema\Article\GetOne;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Component\HttpFoundation\Response;

class ArticleControllerTest extends BaseWebTestCase
{
    use JsonAssertions;

    private const NEW_ARTICLE_TITLE = 'Foo bar';
    private const NEW_ARTICLE_CONTENT = 'Blah blah blah...';

    private const UPDATED_ARTICLE_TITLE = 'Some title';
    private const UPDATED_ARTICLE_CONTENT = 'Nothing here!';

    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ArticleRepository $articleRepository */
        $articleRepository = self::getContainer()->get(ArticleRepository::class);
        $this->articleRepository = $articleRepository;
    }

    public function testGetAll(): void
    {
        $this->loadFixtures(new ArticleGetFixture());

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->client->request('GET', '/api/articles');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonDocumentMatchesSchema($this->getResponseData(), GetAll::get());
    }

    public function testGetOne(): void
    {
        $this->loadFixtures(new ArticleGetFixture());

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $targetArticle = $this->referenceRepository->getReference(ArticleGetFixture::ARTICLE1_REFERENCE, Article::class);

        $this->client->request('GET', sprintf('/api/articles/%d', $targetArticle->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonDocumentMatchesSchema($this->getResponseData(), GetOne::get());
    }

    public function testCreate(): void
    {
        $this->loadFixtures(new UserGetFixture());

        $authorUser = $this->referenceRepository->getReference(UserGetFixture::USER_AUTHOR_REFERENCE, User::class);

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('POST', '/api/articles', [
            'title'     => self::NEW_ARTICLE_TITLE,
            'content'   => self::NEW_ARTICLE_CONTENT,
            'author_id' => $authorUser->getId()
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $newArticle = $this->articleRepository->findOneBy(['title' => self::NEW_ARTICLE_TITLE]);
        $this->assertNotNull($newArticle);

        $this->assertEquals(self::NEW_ARTICLE_TITLE, $newArticle->getTitle());
        $this->assertEquals(self::NEW_ARTICLE_CONTENT, $newArticle->getContent());
        $this->assertEquals($authorUser, $newArticle->getAuthor());

        $this->assertCount(1, $authorUser->getArticles());
        $this->assertContains($newArticle, $authorUser->getArticles());
    }

    public function testUpdate(): void
    {
        $this->loadFixtures(new ArticleGetFixture());

        $targetArticle = $this->referenceRepository->getReference(ArticleGetFixture::ARTICLE1_REFERENCE, Article::class);

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('PUT', sprintf('/api/articles/%d', $targetArticle->getId()), [
            'title'     => self::UPDATED_ARTICLE_TITLE,
            'content'   => self::UPDATED_ARTICLE_CONTENT
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals(self::UPDATED_ARTICLE_TITLE, $targetArticle->getTitle());
        $this->assertEquals(self::UPDATED_ARTICLE_CONTENT, $targetArticle->getContent());
        $this->assertNotNull($targetArticle->getUpdatedAt());
    }

    public function testDelete(): void
    {
        $this->loadFixtures(new ArticleGetFixture());

        $targetArticle = $this->referenceRepository->getReference(ArticleGetFixture::ARTICLE1_REFERENCE, Article::class);
        $targetArticleId = $targetArticle->getId();

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('DELETE', sprintf('/api/articles/%d', $targetArticleId));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedArticle = $this->articleRepository->find($targetArticleId);
        $this->assertNull($deletedArticle);
    }

    private function createUser(UserRole $role): User
    {
        return (new User())
            ->setRole($role);
    }
}