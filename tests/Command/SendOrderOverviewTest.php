<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Tests\Command;

use App\Entity\Hirsch;
use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SendOrderOverviewTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testDailyOrderMailSendWithOrders(): void
    {
        /** @var Paypalmes $payPalMe */
        $payPalMe = $this->entityManager->getRepository(Paypalmes::class)->findAll()[0];
        /** @var Hirsch $hirsch */
        $hirsch = $this->entityManager->getRepository(Hirsch::class)->findAll()[0];

        $order = new Orders();
        $order->setCreated((new DateTime())
            ->setTimezone(new \DateTimeZone('Europe/Berlin')))
            ->setForDate(new DateTime())
            ->setOrderedby('Test')
            ->setHirsch($hirsch);

        $payHistory = new Payhistory();
        $payHistory->setCreated(new DateTime());
        $payHistory->setPaypalme($payPalMe);

        $this->entityManager->persist($payHistory);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $application = new Application(self::$kernel);

        $command = $application->find('order:send:mail');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertEmailCount(1);
    }

    public function testDailyOrderMailSendWithoutOrders(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('order:send:mail');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertEmailCount(0);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testDailyOrderMailSendWithoutActivePayer(): void
    {
        /** @var Hirsch $hirsch */
        $hirsch = $this->entityManager->getRepository(Hirsch::class)->findAll()[0];

        $order = new Orders();
        $order->setCreated((new DateTime())
            ->setTimezone(new \DateTimeZone('Europe/Berlin')))
            ->setForDate(new DateTime())
            ->setOrderedby('Test')
            ->setHirsch($hirsch);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $application = new Application(self::$kernel);

        $command = $application->find('order:send:mail');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertEmailCount(0);
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        parent::tearDown();
    }
}
