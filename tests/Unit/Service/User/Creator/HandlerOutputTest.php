<?php

namespace App\Tests\Unit\Service\User\Creator;

use App\Entity\User;
use App\Service\User\Creator\HandlerOutput;
use PHPUnit\Framework\TestCase;

class HandlerOutputTest extends TestCase
{
    public function testBasics(): void
    {
        $userMock = $this->createMock(User::class);

        $output = new HandlerOutput($userMock, 'abcdabcd');

        $this->assertEquals($userMock, $output->getUser());
        $this->assertEquals('abcdabcd', $output->getPlainPassword());
    }
}