<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Service\Article\Creator\HandlerOutputFactory;
use PHPUnit\Framework\TestCase;

class HandlerOutputFactoryTest extends TestCase
{
    public function testBasics(): void
    {
        $articleMock = $this->createMock(Article::class);

        $factory = new HandlerOutputFactory();

        $output = $factory->create($articleMock, 2);

        $this->assertEquals($articleMock, $output->getArticle());
        $this->assertEquals(2, $output->getAuthorUserId());
    }
}