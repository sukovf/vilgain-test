<?php

namespace App\Tests\Unit\Service\User\Creator;

use App\Entity\User;
use App\Form\User\CreateUserFormType;
use App\Service\Form\ErrorSerializer;
use App\Service\User\Creator\FormHandler;
use App\Service\User\Creator\HandlerOutput;
use App\Service\User\Creator\HandlerOutputFactory;
use App\Service\User\Exception\CreateUserBadRequestException;
use App\Service\User\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandlerTest extends TestCase
{
    private const PLAIN_PASSWORD = 'abcdabcd';

    private User&MockObject $newUserMock;
    /** @var FormInterface<User>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private ErrorSerializer&MockObject $formErrorSerializerMock;
    private HandlerOutputFactory&MockObject $handlerOutputFactoryMock;
    private Request&MockObject $requestMock;
    private FormHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newUserMock = $this->createMock(User::class);

        $userFactoryMock = $this->createMock(Factory::class);
        $userFactoryMock->method('create')->willReturn($this->newUserMock);

        $passwordFieldMock = $this->createMock(FormInterface::class);
        $passwordFieldMock->method('getData')->willReturn(self::PLAIN_PASSWORD);

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock->method('submit')->with([]);
        $this->formMock->method('get')->with('password')->willReturn($passwordFieldMock);

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock
            ->method('create')
            ->with(CreateUserFormType::class, $this->newUserMock)
            ->willReturn($this->formMock);

        $this->formErrorSerializerMock = $this->createMock(ErrorSerializer::class);

        $this->handlerOutputFactoryMock = $this->createMock(HandlerOutputFactory::class);

        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->method('toArray')->willReturn([]);

        $this->handler = new FormHandler(
            $userFactoryMock,
            $formFactoryMock,
            $this->formErrorSerializerMock,
            $this->handlerOutputFactoryMock
        );
    }

    public function testHandle(): void
    {
        $this->formErrorSerializerMock->expects(self::never())->method('serialize');

        $this->formMock
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(true);

        $handlerOutputMock = $this->createMock(HandlerOutput::class);

        $this->handlerOutputFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with($this->newUserMock, self::PLAIN_PASSWORD)
            ->willReturn($handlerOutputMock);

        $this->handler->handle($this->requestMock);
    }

    public function testBadRequest(): void
    {
        $this->formErrorSerializerMock->expects(self::once())->method('serialize');

        $this->formMock
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException(CreateUserBadRequestException::class);

        $this->handler->handle($this->requestMock);
    }
}