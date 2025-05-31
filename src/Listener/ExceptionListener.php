<?php

namespace App\Listener;

use App\Api\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;

        $response = new Response([], $statusCode, $exception->getMessage());

        $event->setResponse($response);
    }
}