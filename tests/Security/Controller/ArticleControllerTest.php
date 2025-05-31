<?php

namespace App\Tests\Security\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Security\UserRole;
use App\Tests\BaseWebTestCase;
use App\Tests\Fixtures\Controller\ControllerSecurity;
use App\Tests\Security\Controller\DataProvider\Article as ArticleDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\HttpFoundation\Response;

class ArticleControllerTest extends BaseWebTestCase
{
    #[DataProviderExternal(ArticleDataProvider::class, 'provideForGet')]
    public function testGetAll(?UserRole $role, bool $isForbidden): void
    {
        if ($role !== null) {
            $this->client->loginUser($this->createUser($role));
        }

        $this->makeAndEvaluateRequest('GET', '/api/articles', $isForbidden);
    }

    #[DataProviderExternal(ArticleDataProvider::class, 'provideForGet')]
    public function testGetOne(?UserRole $role, bool $isForbidden): void
    {
        if ($role !== null) {
            $this->client->loginUser($this->createUser($role));
        }

        $this->makeAndEvaluateRequest('GET', '/api/articles/10', $isForbidden);
    }

    #[DataProviderExternal(ArticleDataProvider::class, 'provideForCreate')]
    public function testCreate(?UserRole $role, bool $isForbidden): void
    {
        if ($role !== null) {
            $this->client->loginUser($this->createUser($role));
        }

        $this->makeAndEvaluateRequest('POST', '/api/articles', $isForbidden);
    }

    #[DataProviderExternal(ArticleDataProvider::class, 'provideForUpdate')]
    public function testUpdate(?string $userEmail, int $articleIndex, bool $isForbidden): void
    {
        $this->loadFixtures(new ControllerSecurity());

        if ($userEmail !== null) {
            $this->client->loginUser($this->loadUser($userEmail));
        }

        $article = $this->loadArticle($articleIndex);

        $this->makeAndEvaluateRequest('PUT', sprintf('/api/articles/%d', $article->getId()), $isForbidden);
    }

    #[DataProviderExternal(ArticleDataProvider::class, 'provideForDelete')]
    public function testDelete(?string $userEmail, int $articleIndex, bool $isForbidden): void
    {
        $this->loadFixtures(new ControllerSecurity());

        if ($userEmail !== null) {
            $this->client->loginUser($this->loadUser($userEmail));
        }

        $article = $this->loadArticle($articleIndex);

        $this->makeAndEvaluateRequest('DELETE', sprintf('/api/articles/%d', $article->getId()), $isForbidden);
    }

    private function makeAndEvaluateRequest(string $requestMethod, string $requestUri, bool $isForbidden): void
    {
        $this->client->request($requestMethod, $requestUri);

        if ($isForbidden) {
            $this->assertTrue(in_array($this->client->getResponse()->getStatusCode(), [Response::HTTP_UNAUTHORIZED, Response::HTTP_FORBIDDEN]));
        } else {
            $this->assertFalse(in_array($this->client->getResponse()->getStatusCode(), [Response::HTTP_UNAUTHORIZED, Response::HTTP_FORBIDDEN]));
        }
    }

    private function createUser(UserRole $role): User
    {
        return (new User())
            ->setRole($role);
    }

    private function loadUser(string $userEmail): User
    {
        return $this->referenceRepository->getReference(sprintf('user.%s', $userEmail), User::class);
    }
    
    private function loadArticle(int $articleIndex): Article
    {
        return $this->referenceRepository->getReference(sprintf('article.%d', $articleIndex), Article::class);
    }
}