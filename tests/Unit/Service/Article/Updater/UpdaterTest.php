<?php

namespace App\Tests\Unit\Service\Article\Updater;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Exception\ArticleNotFoundException;
use App\Service\Article\Exception\ForbiddenException;
use App\Service\Article\Updater\FormHandler;
use App\Service\Article\Updater\Updater;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class UpdaterTest extends TestCase
{
    private User&MockObject $authorUserMock;
    private Article&MockObject $articleMock;
    private Security&MockObject $securityMock;
    private ArticleRepository&MockObject $articleRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;
    private Updater $updater;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorUserMock = $this->createMock(User::class);

        $this->articleMock = $this->createMock(Article::class);
        $this->articleMock->method('getAuthor')->willReturn($this->authorUserMock);

        $this->securityMock = $this->createMock(Security::class);

        $this->requestMock = $this->createMock(Request::class);

        $formHandlerMock = $this->createMock(FormHandler::class);
        $formHandlerMock
            ->method('handle')
            ->with($this->articleMock, $this->requestMock)
            ->willReturn($this->articleMock);

        $this->articleRepositoryMock = $this->createMock(ArticleRepository::class);

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->updater = new Updater(
            $this->securityMock,
            $formHandlerMock,
            $this->articleRepositoryMock,
            $this->entityManagerMock
        );
    }

    public function testUpdate(): void
    {
        $this->articleMock->expects(self::once())->method('setUpdatedAt');

        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::UPDATE, $this->articleMock)
            ->willReturn(true);

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn($this->articleMock);

        $this->entityManagerMock->expects(self::once())->method('persist')->with($this->authorUserMock);
        $this->entityManagerMock->expects(self::once())->method('flush');

        $this->updater->update(5, $this->requestMock);
    }

    public function testArticleDoesNotExist(): void
    {
        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(10)
            ->willReturn(null);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(ArticleNotFoundException::class);

        $this->updater->update(10, $this->requestMock);
    }

    public function testForbidden(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::UPDATE, $this->articleMock)
            ->willReturn(false);

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn($this->articleMock);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(ForbiddenException::class);

        $this->updater->update(5, $this->requestMock);
    }
}