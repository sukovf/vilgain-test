<?php

namespace App\Tests\Integration\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserRole;
use App\Tests\BaseWebTestCase;
use App\Tests\Fixtures\Controller\User\Get;
use App\Tests\Integration\Controller\JsonSchema\User\GetAll;
use App\Tests\Integration\Controller\JsonSchema\User\GetOne;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseWebTestCase
{
    use JsonAssertions;

    private const NEW_USER_EMAIL = 'foo@bar.com';
    private const NEW_USER_NAME = 'Joe';
    private const NEW_USER_ROLE = UserRole::ADMIN;

    private const UPDATED_USER_EMAIL = 'average@joe.com';
    private const UPDATED_USER_NAME = 'Average Joe';
    private const UPDATED_USER_ROLE = UserRole::READER;

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

    public function testGetAll(): void
    {
        $this->loadFixtures(new Get());

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonDocumentMatchesSchema($this->getResponseData(), GetAll::get());
    }

    public function testGetOne(): void
    {
        $this->loadFixtures(new Get());

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $targetUser = $this->referenceRepository->getReference(Get::USER_AUTHOR_REFERENCE, User::class);

        $this->client->request('GET', sprintf('/api/users/%d', $targetUser->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonDocumentMatchesSchema($this->getResponseData(), GetOne::get());
    }

    public function testCreate(): void
    {
        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('POST', '/api/users', [
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

    public function testUpdate(): void
    {
        $this->loadFixtures(new Get());

        $targetUser = $this->referenceRepository->getReference(Get::USER_AUTHOR_REFERENCE, User::class);

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('PUT', sprintf('/api/users/%d', $targetUser->getId()), [
            'email'     => self::UPDATED_USER_EMAIL,
            'name'      => self::UPDATED_USER_NAME,
            'role'      => self::UPDATED_USER_ROLE->value
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals(self::UPDATED_USER_EMAIL, $targetUser->getEmail());
        $this->assertEquals(self::UPDATED_USER_NAME, $targetUser->getName());
        $this->assertEquals(self::UPDATED_USER_ROLE, $targetUser->getRole());
    }

    public function testDelete(): void
    {
        $this->loadFixtures(new Get());

        $targetUser = $this->referenceRepository->getReference(Get::USER_AUTHOR_REFERENCE, User::class);
        $targetUserId = $targetUser->getId();

        $this->client->loginUser($this->createUser(UserRole::ADMIN));

        $this->makeJsonRequest('DELETE', sprintf('/api/users/%d', $targetUserId));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedUser = $this->userRepository->find($targetUserId);
        $this->assertNull($deletedUser);
    }

    private function createUser(UserRole $role): User
    {
        return (new User())
            ->setRole($role);
    }
}