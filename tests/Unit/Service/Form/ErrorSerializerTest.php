<?php

namespace App\Tests\Unit\Service\Form;

use App\Service\Form\ErrorSerializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;

class ErrorSerializerTest extends TestCase
{
    /** @var FormInterface<mixed>&MockObject $formMock */
    private FormInterface&MockObject $formMock;
    private ErrorSerializer $errorSerializer;

    protected function setUp(): void
    {
        $this->formMock = $this->createMock(FormInterface::class);
        $this->errorSerializer = new ErrorSerializer();
    }

    public function testSerializeWithNoErrors(): void
    {
        $errorIterator = new FormErrorIterator($this->formMock, []);

        $this->formMock->expects($this->once())
            ->method('getErrors')
            ->with(true)
            ->willReturn($errorIterator);

        $result = $this->errorSerializer->serialize($this->formMock);

        $this->assertSame('', $result);
    }

    public function testSerializeWithSingleError(): void
    {
        $errorIterator = new FormErrorIterator($this->formMock, [
            $this->createFormError('email', 'Invalid email format')
        ]);

        $this->formMock->expects($this->once())
            ->method('getErrors')
            ->with(true)
            ->willReturn($errorIterator);

        $result = $this->errorSerializer->serialize($this->formMock);

        $this->assertSame('email: Invalid email format', $result);
    }

    public function testSerializeWithMultipleErrors(): void
    {
        $errorIterator = new FormErrorIterator($this->formMock, [
            $this->createFormError('email', 'Invalid email format'),
            $this->createFormError('password', 'Password too short'),
        ]);

        $this->formMock->expects($this->once())
            ->method('getErrors')
            ->with(true)
            ->willReturn($errorIterator);

        $result = $this->errorSerializer->serialize($this->formMock);

        $this->assertSame('email: Invalid email format, password: Password too short', $result);
    }

    public function testSerializeWithNullOrigin(): void
    {
        $error = $this->createFormError(null, 'General form error');
        $errorIterator = new FormErrorIterator($this->formMock, [$error]);

        $this->formMock->expects($this->once())
            ->method('getErrors')
            ->with(true)
            ->willReturn($errorIterator);

        $result = $this->errorSerializer->serialize($this->formMock);

        $this->assertSame('form: General form error', $result);
    }

    private function createFormError(?string $fieldName, string $message): FormError
    {
        $error = new FormError($message);

        if ($fieldName === null) {
            return $error;
        }

        $field = $this->createMock(FormInterface::class);
        $field->method('getName')->willReturn($fieldName);

        $error->setOrigin($field);

        return $error;
    }
}