<?php

namespace App\Tests\Functional\Listener;

use App\Api\Response;
use App\Entity\User;
use App\Listener\JwtEventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class JwtEventListenerTest extends TestCase
{
    private JwtEventListener $listener;

    protected function setUp(): void
    {
        $this->listener = new JwtEventListener();
    }

    public function testOnAuthenticationSucceeded(): void
    {
        $event = new AuthenticationSuccessEvent([], new User(), new SymfonyResponse());

        $this->listener->onAuthenticationSucceeded($event);

        $this->assertEquals([
            'code'      => SymfonyResponse::HTTP_OK,
            'message'   => 'Successfully logged in',
            'data'      => [],
        ], $event->getData());
    }

    public function testOnAuthenticationFailure(): void
    {
        $event = new AuthenticationFailureEvent(new AuthenticationException(), new SymfonyResponse());

        $this->listener->onAuthenticationFailure($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(SymfonyResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testOnJwtNotFound(): void
    {
        $event = new JWTNotFoundEvent(new AuthenticationException());

        $this->listener->onJwtNotFound($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(SymfonyResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testOnJwtInvalid(): void
    {
        $event = new JWTInvalidEvent(new AuthenticationException(), new SymfonyResponse());

        $this->listener->onJwtInvalid($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(SymfonyResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testOnJwtExpired(): void
    {
        $event = new JWTExpiredEvent(new AuthenticationException(), new SymfonyResponse());

        $this->listener->onJwtExpired($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(SymfonyResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}