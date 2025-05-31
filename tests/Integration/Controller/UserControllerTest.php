<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use App\Security\UserRole;
use App\Tests\BaseWebTestCase;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseWebTestCase
{
    use JsonAssertions;

    private const NEW_USER_EMAIL = 'foo@bar.com';
    private const NEW_USER_NAME = 'Joe';
    private const NEW_USER_ROLE = UserRole::ADMIN;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userRepository = $userRepository;
    }

    public function testRegister(): void
    {
        $this->makeJsonRequest('POST', '/api/auth/register', [
            'email'     => self::NEW_USER_EMAIL,
            'password'  => 'abcdabcd',
            'name'      => self::NEW_USER_NAME,
            'role'      => self::NEW_USER_ROLE->value
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $newUser = $this->userRepository->findOneBy(['email' => self::NEW_USER_EMAIL]);
        $this->assertNotNull($newUser);

        $this->assertEquals(self::NEW_USER_NAME, $newUser->getName());
        $this->assertEquals(self::NEW_USER_ROLE, $newUser->getRole());
    }
}