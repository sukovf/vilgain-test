<?php

namespace App\Tests\Unit\Service\Article\Updater;

use App\Entity\Article;
use App\Form\Article\UpdateArticleFormType;
use App\Service\Article\Exception\UpdateArticleBadRequestException;
use App\Service\Form\ErrorSerializer;
use App\Service\Article\Updater\FormHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandlerTest extends TestCase
{
    private Request&MockObject $requestMock;
    private ErrorSerializer&MockObject $formErrorSerializerMock;
    /** @var FormInterface<Article>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private Article&MockObject $articleMock;
    private FormHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleMock = $this->createMock(Article::class);

        $this->formMock = $this->createMock(FormInterface::class);
        $this->formMock->expects(self::once())->method('submit')->with([]);

        $formFactoryMock = $this->createMock(FormFactory::class);
        $formFactoryMock
            ->expects(self::once())
            ->method('create')
            ->with(UpdateArticleFormType::class, $this->articleMock)
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

        $this->handler->handle($this->articleMock, $this->requestMock);
    }

    public function testBadRequest(): void
    {
        $this->formErrorSerializerMock->expects(self::once())->method('serialize');

        $this->formMock
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException(UpdateArticleBadRequestException::class);

        $this->handler->handle($this->articleMock, $this->requestMock);
    }
}