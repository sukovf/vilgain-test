<?php

namespace App\Tests\Unit\Api;

use App\Api\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseTest extends TestCase
{
    public function testBasics(): void
    {
        $response = new Response([
            'foo' => 'bar'
        ], SymfonyResponse::HTTP_CONFLICT, 'Message');

        $this->assertEquals([
            'code'      => SymfonyResponse::HTTP_CONFLICT,
            'message'   => 'Message',
            'data'      => ['foo' => 'bar']
        ], $response->asArray());
    }
}