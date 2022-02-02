<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderTest extends WebTestCase
{
    public function testOrderingAuthenticated(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('test');
        if ($user === null) {
            $this->fail('No user found with username "test"');
        }

        $client->loginUser($user);
        ClockMock::withClockMock(date_create('11:00:00')->getTimestamp());
        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseStatusCodeSame(302);
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        ClockMock::withClockMock(date_create('10:00:00')->getTimestamp());
        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.message.warning', 'Du kannst heute nicht mehr bestellen!');
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
