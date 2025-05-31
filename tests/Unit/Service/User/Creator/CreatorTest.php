<?php

namespace App\Tests\Unit\Service\User\Creator;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Creator\Creator;
use App\Service\User\Creator\FormHandler;
use App\Service\User\Creator\HandlerOutput;
use App\Service\User\Exception\UserExistsException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreatorTest extends TestCase
{
    private const PLAIN_PASSWORD = 'abcdabcd';
    private const HASHED_PASSWORD = 'hashed_password';
    private const USER_ID = 10;

    private User&MockObject $newUserMock;
    private UserRepository&MockObject $userRepositoryMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private Request&MockObject $requestMock;
    private Creator $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newUserMock = $this->createMock(User::class);
        $this->newUserMock->method('getEmail');
        $this->newUserMock->method('setPassword')->with('hashed_password');
        $this->newUserMock->method('getId')->willReturn(self::USER_ID);

        $formHandlerOutputMock = $this->createMock(HandlerOutput::class);
        $formHandlerOutputMock->method('getUser')->willReturn($this->newUserMock);
        $formHandlerOutputMock->method('getPlainPassword')->willReturn(self::PLAIN_PASSWORD);

        $formHandlerMock = $this->createMock(FormHandler::class);
        $formHandlerMock->method('handle')->willReturn($formHandlerOutputMock);

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasherMock
            ->method('hashPassword')
            ->with($this->newUserMock, self::PLAIN_PASSWORD)
            ->willReturn(self::HASHED_PASSWORD);

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->requestMock = $this->createMock(Request::class);

        $this->creator = new Creator(
            $formHandlerMock,
            $this->userRepositoryMock,
            $passwordHasherMock,
            $this->entityManagerMock
        );
    }

    public function testCreate(): void
    {
        $this->userRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($this->newUserMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $newUserId = $this->creator->create($this->requestMock);

        $this->assertEquals(self::USER_ID, $newUserId);
    }

    public function testUserAlreadyExists(): void
    {
        $existingUserMock = $this->createMock(User::class);

        $this->userRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($existingUserMock);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        $this->expectException(UserExistsException::class);

        $this->creator->create($this->requestMock);
    }
}