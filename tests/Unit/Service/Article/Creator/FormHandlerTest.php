<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Form\Article\CreateArticleFormType;
use App\Service\Article\Factory;
use App\Service\Article\Exception\CreateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use App\Service\Article\Creator\FormHandler;
use App\Service\Article\Creator\HandlerOutput;
use App\Service\Article\Creator\HandlerOutputFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandlerTest extends TestCase
{
    private const AUTHOR_USER_ID = 10;

    private Article&MockObject $newArticleMock;
    private ErrorSerializer&MockObject $formErrorSerializerMock;
    /** @var FormInterface<Article>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private HandlerOutputFactory&MockObject $handlerOutputFactoryMock;
    private Request&MockObject $requestMock;
    private FormHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newArticleMock = $this->createMock(Article::class);

        $articleFactoryMock = $this->createMock(Factory::class);
        $articleFactoryMock->expects(self::once())->method('create')->willReturn($this->newArticleMock);

        $this->formErrorSerializerMock = $this->createMock(ErrorSerializer::class);

        $authorIdFieldMock = $this->createMock(FormInterface::class);
        $authorIdFieldMock->method('getData')->willReturn(self::AUTHOR_USER_ID);

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock->expects(self::once())->method('submit')->with([]);
        $this->formMock->method('get')->with('author_id')->willReturn($authorIdFieldMock);

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with(CreateArticleFormType::class, $this->newArticleMock)
            ->willReturn($this->formMock);

        $this->handlerOutputFactoryMock = $this->createMock(HandlerOutputFactory::class);

        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->expects(self::once())->method('toArray')->willReturn([]);

        $this->handler = new FormHandler(
            $articleFactoryMock,
            $formFactoryMock,
            $this->formErrorSerializerMock,
            $this->handlerOutputFactoryMock
        );
    }

    public function testHandle(): void
    {
        $this->formErrorSerializerMock->expects(self::never())->method('serialize');

        $this->formMock->expects(self::once())->method('isValid')->willReturn(true);

        $handlerOutputMock = $this->createMock(HandlerOutput::class);

        $this->handlerOutputFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with($this->newArticleMock, self::AUTHOR_USER_ID)
            ->willReturn($handlerOutputMock);

        $this->handler->handle($this->requestMock);
    }

    public function testBadRequest(): void
    {
        $this->formErrorSerializerMock->expects(self::once())->method('serialize');

        $this->formMock->expects(self::once())->method('isValid')->willReturn(false);

        $this->expectException(CreateArticleBadRequestException::class);

        $this->handler->handle($this->requestMock);
    }
}