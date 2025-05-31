<?php

namespace App\Tests\Security\Controller;

use App\Entity\User;
use App\Security\UserRole;
use PHPUnit\Framework\Attributes\DataProvider;
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