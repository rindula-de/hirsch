<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Tests\Controller;

use App\Entity\Holidays;
use App\Tests\TestHelper\TestCurlHttpClient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Component\Stopwatch\Stopwatch;

class ModalControllerTest extends WebTestCase
{
    private EntityManager $entityManager;
    private KernelBrowser $client;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();

        // prepare environment
        $cache = new FilesystemAdapter();
        $cache->clear();

        /** @var Stopwatch $stopwatch */
        $stopwatch = $this->getContainer()->get('debug.stopwatch');
        $httpCurlClient = new TestCurlHttpClient($this->getContainer(), 'https://api.github.com/repos/Rindula/hirsch/');
        $httpClient = new TraceableHttpClient($httpCurlClient, $stopwatch);
        $this->getContainer()->set('http_client', $httpClient);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testInHolidays(): void
    {
        $holiday = new Holidays();
        $holiday->setStart(new \DateTime('-1 day'));
        $holiday->setEnd(new \DateTime('+1 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/modalInformationText');
        self::assertResponseIsSuccessful();
        $this->assertNotEmpty($this->client->getResponse()->getContent());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testNotInHolidays(): void
    {
        $holiday = new Holidays();
        $holiday->setStart(new \DateTime('+1 day'));
        $holiday->setEnd(new \DateTime('+3 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/modalInformationText');
        self::assertResponseIsSuccessful();
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        parent::tearDown();
    }
}
