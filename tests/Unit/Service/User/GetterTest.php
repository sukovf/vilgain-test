<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Exception\UserNotFoundException;
use App\Service\User\Getter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetterTest extends TestCase
{
    private const USER_ID = 10;

    private UserRepository&MockObject $userRepositoryMock;
    private Getter $getter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this->getter = new Getter($this->userRepositoryMock);
    }

    public function testGetAll(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([
                $this->createMock(User::class),
                $this->createMock(User::class)
            ]);

        $users = $this->getter->getAll();

        self::assertCount(2, $users);
    }

    public function testGetOne(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn($this->createMock(User::class));

        $this->getter->getOne(self::USER_ID);
    }

    public function testGetNonExistent(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $this->getter->getOne(self::USER_ID);
    }
}