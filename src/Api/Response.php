<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class Response extends JsonResponse
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(
        private readonly mixed  $rawData = null,
        private readonly int    $status = 200,
        private readonly string $message = '',
        array                   $headers = [],
        bool                    $json = false)
    {
        parent::__construct($this->asArray(), $status, $headers, $json);
    }

    /**
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        return [
            'code'      => $this->status,
            'message'   => $this->message,
            'data'      => $this->rawData,
        ];
    }
}