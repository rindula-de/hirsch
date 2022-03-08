<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use App\Entity\Holidays;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HolidayControllerTest extends WebTestCase
{
    private EntityManager $entityManager;
    private KernelBrowser $client;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();
    }

    public function testNoHolidays(): void
    {
        $this->client->request('GET', '/holidays');
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($response);
        $this->assertStringContainsString('Neuer Urlaub', $response);
        $this->assertStringNotContainsString('Bearbeiten', $response);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testHolidaysInList(): void
    {
        $holiday = new Holidays();
        $holiday->setStart(new \DateTime('-5 day'));
        $holiday->setEnd(new \DateTime('-2 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/holidays');
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($response);
        $this->assertStringContainsString('Neuer Urlaub', $response);
        $this->assertStringContainsString('Bearbeiten', $response);
    }

    public function testApiReturnEmptyResponse(): void
    {
        $this->client->request('GET', '/api/holidays');
        $this->assertResponseIsSuccessful();
        $this->assertEquals('[]', $this->client->getResponse()->getContent());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \Exception
     */
    public function testApiReturnValidResponse(): void
    {
        $holiday = new Holidays();
        $start = new \DateTime('-5 day');
        $holiday->setStart($start);
        $holiday->setEnd(new \DateTime('-2 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/holidays');
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        $this->assertNotEmpty($response);
        if (!is_string($response)) {
            throw new \Exception('Empty response');
        }
        $this->assertJson($response);
        $jsonResponse = json_decode($response);
        if (!is_array($jsonResponse)) {
            throw new \Exception('Not a valid json array in response');
        }
        $this->assertEquals($start->format("Y-m-d\TH:i:sP"), $jsonResponse[0]->start);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        parent::tearDown();
    }
}
