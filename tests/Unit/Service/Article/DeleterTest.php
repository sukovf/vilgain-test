<?php

namespace App\Tests\Unit\Service\Article;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Deleter;
use App\Service\Article\Exception\ArticleNotFoundException;
use App\Service\Article\Exception\ForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class DeleterTest extends TestCase
{
    private const ARTICLE_ID = 10;

    private Article&MockObject $articleMock;
    private ArticleRepository&MockObject $articleRepositoryMock;
    private Security&MockObject $securityMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Deleter $deleter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleMock = $this->createMock(Article::class);

        $this->articleRepositoryMock = $this->createMock(ArticleRepository::class);

        $this->securityMock = $this->createMock(Security::class);

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->deleter = new Deleter($this->securityMock, $this->articleRepositoryMock, $this->entityManagerMock);
    }

    public function testDelete(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::DELETE, $this->articleMock)
            ->willReturn(true);

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::ARTICLE_ID)
            ->willReturn($this->articleMock);

        $this->entityManagerMock->expects(self::once())->method('remove')->with($this->articleMock);
        $this->entityManagerMock->expects(self::once())->method('flush');

        $this->deleter->delete(self::ARTICLE_ID);
    }

    public function testForbidden(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::DELETE, $this->articleMock)
            ->willReturn(false);

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::ARTICLE_ID)
            ->willReturn($this->articleMock);

        $this->entityManagerMock->expects(self::never())->method('remove');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(ForbiddenException::class);

        $this->deleter->delete(self::ARTICLE_ID);
    }

    public function testNonExistentArticle(): void
    {
        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::ARTICLE_ID)
            ->willReturn(null);

        $this->entityManagerMock->expects(self::never())->method('remove');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(ArticleNotFoundException::class);

        $this->deleter->delete(self::ARTICLE_ID);
    }
}