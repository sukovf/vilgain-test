<?php

namespace App\Tests\Unit\Service\User\Creator;

use App\Entity\User;
use App\Service\User\Creator\HandlerOutputFactory;
use PHPUnit\Framework\TestCase;

class HandlerOutputFactoryTest extends TestCase
{
    public function testBasics(): void
    {
        $userMock = $this->createMock(User::class);

        $factory = new HandlerOutputFactory();

        $output = $factory->create($userMock, 'abcdabcd');

        $this->assertEquals($userMock, $output->getUser());
        $this->assertEquals('abcdabcd', $output->getPlainPassword());
    }
}