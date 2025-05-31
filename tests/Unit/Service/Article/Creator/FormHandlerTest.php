<?php

namespace App\Tests\Unit\Service\Article\Creator;

use App\Entity\Article;
use App\Form\Article\CreateArticleFormType;
use App\Service\Article\Factory;
use App\Service\Article\Exception\CreateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use App\Service\Article\Creator\FormHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandlerTest extends TestCase
{
    private ErrorSerializer&MockObject $formErrorSerializerMock;
    /** @var FormInterface<Article>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private Request&MockObject $requestMock;
    private FormHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $newArticleMock = $this->createMock(Article::class);

        $articleFactoryMock = $this->createMock(Factory::class);
        $articleFactoryMock->expects(self::once())->method('create')->willReturn($newArticleMock);

        $this->formErrorSerializerMock = $this->createMock(ErrorSerializer::class);

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock->expects(self::once())->method('submit')->with([]);

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with(CreateArticleFormType::class, $newArticleMock)
            ->willReturn($this->formMock);

        $this->requestMock = $this->createMock(Request::class);
        $this->requestMock->expects(self::once())->method('toArray')->willReturn([]);

        $this->handler = new FormHandler(
            $articleFactoryMock,
            $formFactoryMock,
            $this->formErrorSerializerMock
        );
    }

    public function testHandle(): void
    {
        $this->formErrorSerializerMock->expects(self::never())->method('serialize');

        $this->formMock->expects(self::once())->method('isValid')->willReturn(true);

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