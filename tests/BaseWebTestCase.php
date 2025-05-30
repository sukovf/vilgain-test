<?php

namespace App\Tests;

use App\Tests\Exception\InvalidArgumentException;
use App\Tests\Exception\RuntimeException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ReferenceRepository $referenceRepository;
    private ORMExecutor $ormExecutor;
    private Loader $fixtureLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine.orm.entity_manager');

        $this->fixtureLoader = new Loader();

        $this->ormExecutor = new ORMExecutor($em, new ORMPurger());
        $this->ormExecutor->execute($this->fixtureLoader->getFixtures());

        $this->referenceRepository = $this->ormExecutor->getReferenceRepository();
    }

    /**
     * @param Fixture|Fixture[] $fixtures
     */
    protected function loadFixtures(Fixture|array $fixtures): void
    {
        $fixtureArray = is_array($fixtures) ? $fixtures : [$fixtures];

        foreach ($fixtureArray as $fixture) {
            $this->fixtureLoader->addFixture($fixture);
        }

        $this->ormExecutor->execute($this->fixtureLoader->getFixtures());
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function makeJsonRequest(string $method, string $uri, array $data = []): void
    {
        $encodedData = json_encode($data);
        if (!is_string($encodedData)) {
            throw new InvalidArgumentException('Invalid data');
        }

        $this->client->request($method, $uri, server: ['CONTENT_TYPE' => 'application/json'], content: $encodedData);
    }

    /**
     * @return array<mixed, mixed>
     */
    protected function getResponseData(): array
    {
        $responseContent = $this->client->getResponse()->getContent();
        if (!is_string($responseContent)) {
            throw new RuntimeException('The response content is not a string');
        }

        $decodedData = json_decode($responseContent, true);
        if (!is_array($decodedData)) {
            throw new RuntimeException('The response content is not a valid JSON');
        }

        return $decodedData;
    }
}