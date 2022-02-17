<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use App\Entity\Holidays;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModalControllerTest extends WebTestCase
{
    private EntityManager $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();
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
        $this->assertResponseIsSuccessful();
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
        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        parent::tearDown();
    }
}
