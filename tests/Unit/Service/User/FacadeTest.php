<?php

namespace App\Tests\Unit\Service\User;

use App\Service\User\Creator\Creator;
use App\Service\User\Facade;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FacadeTest extends TestCase
{
    public function testRegistration(): void
    {
        $requestMock = $this->createMock(Request::class);

        $creatorMock = $this->createMock(Creator::class);
        $creatorMock
            ->expects(self::once())
            ->method('create')
            ->with($requestMock)
            ->willReturn(11);

        $userFacade = new Facade($creatorMock);

        $newUserId = $userFacade->create($requestMock);

        self::assertEquals(11, $newUserId);
    }
}