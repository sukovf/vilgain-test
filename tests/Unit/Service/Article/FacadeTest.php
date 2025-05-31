<?php

namespace App\Tests\Unit\Service\Article;

use App\Entity\Article;
use App\Service\Article\Facade;
use App\Service\Article\Creator\Creator;
use App\Service\Article\Updater\Updater;
use App\Service\Article\Deleter;
use App\Service\Article\Getter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FacadeTest extends TestCase
{
    private Request&MockObject $requestMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Request::class);
    }

    public function testGetArticles(): void
    {
        $getterMock = $this->createMock(Getter::class);
        $getterMock
            ->expects(self::once())
            ->method('getAll')
            ->willReturn([
                $this->createMock(Article::class),
                $this->createMock(Article::class)
            ]);

        $articleFacade = new Facade(
            $getterMock,
            $this->createMock(Creator::class),
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $articles = $articleFacade->getAll();

        self::assertCount(2, $articles);
    }

    public function testGetArticle(): void
    {
        $getterMock = $this->createMock(Getter::class);
        $getterMock
            ->expects(self::once())
            ->method('getOne')
            ->with(5)
            ->willReturn($this->createMock(Article::class));

        $articleFacade = new Facade(
            $getterMock,
            $this->createMock(Creator::class),
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $articleFacade->getOne(5);
    }

    public function testCreate(): void
    {
        $creatorMock = $this->createMock(Creator::class);
        $creatorMock
            ->expects(self::once())
            ->method('create')
            ->with($this->requestMock)
            ->willReturn(11);

        $articleFacade = new Facade(
            $this->createMock(Getter::class),
            $creatorMock,
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $newArticleId = $articleFacade->create($this->requestMock);

        self::assertEquals(11, $newArticleId);
    }

    public function testUpdate(): void
    {
        $updaterMock = $this->createMock(Updater::class);
        $updaterMock
            ->expects(self::once())
            ->method('update')
            ->with(5, $this->requestMock);

        $articleFacade = new Facade(
            $this->createMock(Getter::class),
            $this->createMock(Creator::class),
            $updaterMock,
            $this->createMock(Deleter::class)
        );

        $articleFacade->update(5, $this->requestMock);
    }

    public function testDelete(): void
    {
        $deleterMock = $this->createMock(Deleter::class);
        $deleterMock
            ->expects(self::once())
            ->method('delete')
            ->with(5);

        $articleFacade = new Facade(
            $this->createMock(Getter::class),
            $this->createMock(Creator::class),
            $this->createMock(Updater::class),
            $deleterMock
        );

        $articleFacade->delete(5);
    }
}