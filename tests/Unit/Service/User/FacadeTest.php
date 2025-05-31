<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Service\User\Creator\Creator;
use App\Service\User\Deleter;
use App\Service\User\Facade;
use App\Service\User\Getter;
use App\Service\User\Updater\Updater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FacadeTest extends TestCase
{
    private Request&MockObject $requestMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Request::class);
    }

    public function testRegistration(): void
    {
        $creatorMock = $this->createMock(Creator::class);
        $creatorMock
            ->expects(self::once())
            ->method('create')
            ->with($this->requestMock)
            ->willReturn(11);

        $userFacade = new Facade(
            $creatorMock,
            $this->createMock(Getter::class),
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $newUserId = $userFacade->create($this->requestMock);

        self::assertEquals(11, $newUserId);
    }

    public function testGetUsers(): void
    {
        $getterMock = $this->createMock(Getter::class);
        $getterMock
            ->expects(self::once())
            ->method('getAll')
            ->willReturn([
                $this->createMock(User::class),
                $this->createMock(User::class)
            ]);

        $userFacade = new Facade(
            $this->createMock(Creator::class),
            $getterMock,
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $users = $userFacade->getAll();

        self::assertCount(2, $users);
    }

    public function testGetUser(): void
    {
        $getterMock = $this->createMock(Getter::class);
        $getterMock
            ->expects(self::once())
            ->method('getOne')
            ->with(5)
            ->willReturn($this->createMock(User::class));

        $userFacade = new Facade(
            $this->createMock(Creator::class),
            $getterMock,
            $this->createMock(Updater::class),
            $this->createMock(Deleter::class)
        );

        $userFacade->getOne(5);
    }

    public function testUpdate(): void
    {
        $updaterMock = $this->createMock(Updater::class);
        $updaterMock
            ->expects(self::once())
            ->method('update')
            ->with(5, $this->requestMock);

        $userFacade = new Facade(
            $this->createMock(Creator::class),
            $this->createMock(Getter::class),
            $updaterMock,
            $this->createMock(Deleter::class)
        );

        $userFacade->update(5, $this->requestMock);
    }

    public function testDelete(): void
    {
        $deleterMock = $this->createMock(Deleter::class);
        $deleterMock
            ->expects(self::once())
            ->method('delete')
            ->with(5);

        $userFacade = new Facade(
            $this->createMock(Creator::class),
            $this->createMock(Getter::class),
            $this->createMock(Updater::class),
            $deleterMock
        );

        $userFacade->delete(5);
    }
}