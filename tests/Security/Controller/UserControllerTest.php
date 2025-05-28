<?php

namespace App\Tests\Security\Controller;

use App\Entity\User;
use App\Security\UserRole;
use App\Tests\Security\Controller\DataProvider\OtherUserActions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    #[DataProvider('getRolesForRegister')]
    public function testRegister(?UserRole $role): void
    {
        $client = static::createClient();

        if ($role !== null) {
            $client->loginUser($this->createUser($role));
        }

        $client->request('POST', '/api/auth/register');

        $this->assertNotEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    #[DataProviderExternal(OtherUserActions::class, 'get')]
    public function testOtherUserActions(string $requestMethod, string $requestUri, ?UserRole $role, bool $isForbidden): void
    {
        $client = static::createClient();

        if ($role !== null) {
            $client->loginUser($this->createUser($role));
        }

        $client->request($requestMethod, $requestUri);

        if ($isForbidden) {
            $this->assertTrue(in_array(Response::HTTP_FORBIDDEN, [Response::HTTP_UNAUTHORIZED, Response::HTTP_FORBIDDEN]));
        } else {
            $this->assertNotEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        }
    }

    private function createUser(UserRole $role): User
    {
        return (new User())
            ->setRole($role);
    }

    /**
     * @return array<string, array<string, ?UserRole>>
     */
    public static function getRolesForRegister(): array
    {
        return [
            'UNAUTHENTICATED'   => [
                'role' => null
            ],
            'ADMIN'             => [
                'role' => UserRole::ADMIN
            ],
            'AUTHOR'            => [
                'role' => UserRole::AUTHOR
            ],
            'READER'            => [
                'role' => UserRole::READER
            ]
        ];
    }
}