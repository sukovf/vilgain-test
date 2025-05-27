<?php

namespace App\Tests\Unit\Listener;

use App\Api\Response;
use App\Listener\ExceptionListener;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class ExceptionListenerTest extends TestCase
{
    private ExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ExceptionListener();
    }

    public function testHandlesHttpException(): void
    {
        // Arrange
        $exception = new HttpException(400, 'Bad Request');
        $event = $this->createExceptionEvent($exception);

        // Act
        $this->listener->onException($event);

        // Assert
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);

        $content = $response->getContent();
        $this->assertIsString($content);

        /** @var array{code: int, message: string, data: mixed} $responseData */
        $responseData = json_decode($content, true);
        $this->assertEquals(400, $responseData['code']);
        $this->assertEquals('Bad Request', $responseData['message']);
        $this->assertEquals([], $responseData['data']);
    }

    public function testHandlesGenericException(): void
    {
        // Arrange
        $exception = new Exception('Something went wrong');
        $event = $this->createExceptionEvent($exception);

        // Act
        $this->listener->onException($event);

        // Assert
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);

        $content = $response->getContent();
        $this->assertIsString($content);

        /** @var array{code: int, message: string, data: mixed} $responseData */
        $responseData = json_decode($content, true);
        $this->assertEquals(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $responseData['code']);
        $this->assertEquals('Something went wrong', $responseData['message']);
        $this->assertEquals([], $responseData['data']);
    }

    private function createExceptionEvent(Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $requestType = HttpKernelInterface::MAIN_REQUEST;

        return new ExceptionEvent(
            $kernel,
            $request,
            $requestType,
            $exception
        );
    }
}