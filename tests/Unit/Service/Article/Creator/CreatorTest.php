<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Creator\Creator;
use App\Service\Article\Creator\FormHandler;
use App\Service\Article\Creator\HandlerOutput;
use App\Service\Article\Exception\ForbiddenException;
use App\Service\User\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class CreatorTest extends TestCase
{
    private const ARTICLE_ID = 11;
    private const AUTHOR_ID = 2;

    private Security&MockObject $securityMock;
    private UserRepository&MockObject $userRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;
    private Article&MockObject $newArticleMock;
    private User&MockObject $authorUserMock;
    private Creator $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityMock = $this->createMock(Security::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->requestMock = $this->createMock(Request::class);

        $this->newArticleMock = $this->createMock(Article::class);
        $this->newArticleMock->method('getId')->willReturn(self::ARTICLE_ID);

        $this->authorUserMock = $this->createMock(User::class);

        $formHandlerOutputMock = $this->createMock(HandlerOutput::class);
        $formHandlerOutputMock->method('getArticle')->willReturn($this->newArticleMock);
        $formHandlerOutputMock->method('getAuthorUserId')->willReturn(self::AUTHOR_ID);

        $formHandlerMock = $this->createMock(FormHandler::class);
        $formHandlerMock->method('handle')->willReturn($formHandlerOutputMock);

        $this->creator = new Creator(
            $this->securityMock,
            $formHandlerMock,
            $this->userRepositoryMock,
            $this->entityManagerMock
        );
    }

    public function testCreate(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::CREATE, self::isInstanceOf(Article::class))
            ->willReturn(true);

        $this->authorUserMock->expects(self::once())->method('addArticle')->with($this->newArticleMock);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(2)
            ->willReturn($this->authorUserMock);

        $this->entityManagerMock->expects(self::once())->method('persist')->with($this->authorUserMock);
        $this->entityManagerMock->expects(self::once())->method('flush');

        $newArticleId = $this->creator->create($this->requestMock);

        $this->assertEquals(self::ARTICLE_ID, $newArticleId);
    }

    public function testForbidden(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::CREATE, self::isInstanceOf(Article::class))
            ->willReturn(false);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(ForbiddenException::class);

        $this->creator->create($this->requestMock);
    }

    public function testAuthorUSerNotFound(): void
    {
        $this->securityMock
            ->expects(self::once())
            ->method('isGranted')
            ->with(ArticleVoter::CREATE, self::isInstanceOf(Article::class))
            ->willReturn(true);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(2)
            ->willReturn(null);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(UserNotFoundException::class);

        $this->creator->create($this->requestMock);
    }
}