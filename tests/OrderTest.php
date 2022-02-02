<?php

namespace App\Tests;

use App\Controller\OrderController;
use App\Entity\Orders;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group time-sensitive
 */
class OrderTest extends WebTestCase
{
    public function testOrderingAuthenticated(): void
    {
        ClockMock::register(Orders::class);
        ClockMock::register(OrderController::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('test');
        if ($user === null) {
            $this->fail('No user found with username "test"');
        }
        
        $client = static::createClient();
        $client->loginUser($user);
        $date = date_create('12:00:00');
        if (!$date) {
            $this->fail('Could not create date 12:00:00');
        }
        ClockMock::withClockMock($date->getTimestamp());
        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseRedirects('/karte', 302);
        $date = date_create('08:00:00');
        if (!$date) {
            $this->fail('Could not create date 08:00:00');
        }
        ClockMock::withClockMock($date->getTimestamp());
        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => 'Max Mustermann',
            'order[note]'      => '', ]);
        $this->assertResponseRedirects('/zahlen-bitte/', 302);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => 'Max Mustermann',
            'order[note]'      => '+ Pommes', ]);
        $this->assertResponseRedirects('/zahlen-bitte/', 302);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => '',
            'order[note]'      => '', ]);
        $this->assertResponseStatusCodeSame(500);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => '',
            'order[note]'      => '+ Pommes', ]);
        $this->assertResponseStatusCodeSame(500);
    }

    public function testOrderingUnauthenticated(): void
    {
        $client = static::createClient();

        $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseStatusCodeSame(401);
    }
}
