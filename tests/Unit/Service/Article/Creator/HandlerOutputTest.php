<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Service\Article\Creator\HandlerOutput;
use PHPUnit\Framework\TestCase;

class HandlerOutputTest extends TestCase
{
    public function testBasics(): void
    {
        $articleMock = $this->createMock(Article::class);

        $output = new HandlerOutput($articleMock, 2);

        $this->assertEquals($articleMock, $output->getArticle());
        $this->assertEquals(2, $output->getAuthorUserId());
    }
}