<?php

namespace App\Tests\Unit\Service\User\Updater;

use App\Entity\User;
use App\Form\User\UpdateUserFormType;
use App\Service\Form\ErrorSerializer;
use App\Service\User\Exception\UpdateUserBadRequestException;
use App\Service\User\Updater\FormHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandlerTest extends TestCase
{
    private User&MockObject $userMock;
    /** @var FormInterface<User>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private ErrorSerializer&MockObject $formErrorSerializerMock;
    private Request&MockObject $requestMock;
    private FormHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userMock = $this->createMock(User::class);

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock->expects(self::once())->method('submit')->with([]);

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with(UpdateUserFormType::class, $this->userMock)
            ->willReturn($this->formMock);

        $this->formErrorSerializerMock = $this->createMock(ErrorSerializer::class);

        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->expects(self::once())->method('toArray')->willReturn([]);

        $this->handler = new FormHandler($formFactoryMock, $this->formErrorSerializerMock);
    }

    public function testHandle(): void
    {
        $this->formErrorSerializerMock->expects(self::never())->method('serialize');

        $this->formMock
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $this->handler->handle($this->userMock, $this->requestMock);
    }

    public function testBadRequest(): void
    {
        $this->formErrorSerializerMock->expects(self::once())->method('serialize');

        $this->formMock
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException(UpdateUserBadRequestException::class);

        $this->handler->handle($this->userMock, $this->requestMock);
    }
}