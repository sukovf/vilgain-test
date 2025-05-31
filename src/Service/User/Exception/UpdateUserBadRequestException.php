<?php

namespace App\Service\User\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class UpdateUserBadRequestException extends HttpException
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(string $message = '', ?Throwable $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
    }
}