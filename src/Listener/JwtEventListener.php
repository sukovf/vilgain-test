<?php

namespace App\Listener;

use App\Api\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class JwtEventListener
{
    public function onAuthenticationSucceeded(AuthenticationSuccessEvent $event): void
    {
        $response = new Response($event->getData(), SymfonyResponse::HTTP_OK, 'Successfully logged in');

        $event->setData($response->asArray());
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $response = new Response([], SymfonyResponse::HTTP_UNAUTHORIZED, 'Invalid credentials');

        $event->setResponse($response);
    }

    public function onJwtNotFound(JWTNotFoundEvent $event): void
    {
        $response = new Response([], SymfonyResponse::HTTP_UNAUTHORIZED, 'JWT token not found');

        $event->setResponse($response);
    }

    public function onJwtInvalid(JWTInvalidEvent $event): void
    {
        $response = new Response([], SymfonyResponse::HTTP_UNAUTHORIZED, 'JWT token is invalid');

        $event->setResponse($response);
    }

    public function onJwtExpired(JWTExpiredEvent $event): void
    {
        $response = new Response([], SymfonyResponse::HTTP_UNAUTHORIZED, 'JWT token has expired');

        $event->setResponse($response);
    }
}