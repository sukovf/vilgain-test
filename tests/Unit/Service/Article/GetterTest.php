<?php

namespace App\Tests\Unit\Service\Article;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\Article\Exception\ArticleNotFoundException;
use App\Service\Article\Getter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetterTest extends TestCase
{
    private const ARTICLE_ID = 10;

    private ArticleRepository&MockObject $articleRepositoryMock;
    private Getter $getter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepositoryMock = $this->createMock(ArticleRepository::class);

        $this->getter = new Getter($this->articleRepositoryMock);
    }

    public function testGetAll(): void
    {
        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([
                $this->createMock(Article::class),
                $this->createMock(Article::class)
            ]);

        $articles = $this->getter->getAll();

        self::assertCount(2, $articles);
    }

    public function testGetOne(): void
    {
        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::ARTICLE_ID)
            ->willReturn($this->createMock(Article::class));

        $this->getter->getOne(self::ARTICLE_ID);
    }

    public function testGetNonExistent(): void
    {
        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::ARTICLE_ID)
            ->willReturn(null);

        $this->expectException(ArticleNotFoundException::class);

        $this->getter->getOne(self::ARTICLE_ID);
    }
}