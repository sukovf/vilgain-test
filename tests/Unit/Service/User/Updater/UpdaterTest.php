<?php

namespace App\Tests\Unit\Service\User\Updater;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Exception\UserNotFoundException;
use App\Service\User\Updater\FormHandler;
use App\Service\User\Updater\Updater;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UpdaterTest extends TestCase
{
    private const USER_ID = 10;

    private User&MockObject $userMock;
    private UserRepository&MockObject $userRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;
    private Updater $updater;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userMock = $this->createMock(User::class);

        $formHandlerMock = $this->createMock(FormHandler::class);
        $formHandlerMock->method('handle')->willReturn($this->userMock);

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->requestMock = $this->createMock(Request::class);

        $this->updater = new Updater(
            $formHandlerMock,
            $this->userRepositoryMock,
            $this->entityManagerMock
        );
    }

    public function testUpdate(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn($this->userMock);

        $this->entityManagerMock->expects(self::once())->method('persist')->with($this->userMock);
        $this->entityManagerMock->expects(self::once())->method('flush');

        $this->updater->update(self::USER_ID, $this->requestMock);
    }

    public function testUserDoesNotExist(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with(self::USER_ID)
            ->willReturn(null);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(UserNotFoundException::class);

        $this->updater->update(self::USER_ID, $this->requestMock);
    }
}