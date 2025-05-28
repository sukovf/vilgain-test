<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Deleter;
use App\Service\User\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleterTest extends TestCase
{
    private const USER_ID = 10;

    private UserRepository&MockObject $userRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Deleter $deleter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->deleter = new Deleter($this->userRepositoryMock, $this->entityManagerMock);
    }

    public function testDelete(): void
    {
        $userMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn($userMock);

        $this->entityManagerMock->expects(self::once())->method('remove')->with($userMock);
        $this->entityManagerMock->expects(self::once())->method('flush');

        $this->deleter->delete(self::USER_ID);
    }

    public function testNonExistentUser(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn(null);

        $this->entityManagerMock->expects(self::never())->method('remove');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(UserNotFoundException::class);

        $this->deleter->delete(self::USER_ID);
    }
}