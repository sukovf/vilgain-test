<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Entity\User;
use App\Security\Voter\ArticleVoter;
use App\Service\Article\Creator\Creator;
use App\Service\Article\Creator\FormHandler;
use App\Service\Article\Exception\ForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class CreatorTest extends TestCase
{
    private const ARTICLE_ID = 11;

    private Security&MockObject $securityMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;
    private Article&MockObject $newArticleMock;
    private User&MockObject $authorUserMock;
    private Creator $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorUserMock = $this->createMock(User::class);

        $this->securityMock = $this->createMock(Security::class);
        $this->securityMock->method('getUser')->willReturn($this->authorUserMock);

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->requestMock = $this->createMock(Request::class);

        $this->newArticleMock = $this->createMock(Article::class);
        $this->newArticleMock->method('getId')->willReturn(self::ARTICLE_ID);

        $formHandlerMock = $this->createMock(FormHandler::class);
        $formHandlerMock->method('handle')->willReturn($this->newArticleMock);

        $this->creator = new Creator(
            $this->securityMock,
            $formHandlerMock,
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
}