<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests;

use App\Controller\OrderController;
use App\Repository\UserRepository;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group time-sensitive
 */
class OrderTest extends WebTestCase
{
    public function testOrderingAuthenticatedTooLate(): void
    {
        $this->markAsRisky();
        ClockMock::register(OrderController::class);
        ClockMock::withClockMock(strtotime('12:00'));
        $client = static::createClient([], ['REMOTE_ADDR' => '1.1.1.1']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('test');
        if ($user === null) {
            $this->fail('No user found with username "test"');
        }

        $client->loginUser($user);
        $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseRedirects('/karte', 302);
        ClockMock::withClockMock(false);
    }

    public function testOrderingAuthenticatedInTime(): void
    {
        $this->markAsRisky();
        ClockMock::register(OrderController::class);
        ClockMock::withClockMock(strtotime('08:00'));
        $client = static::createClient([], ['REMOTE_ADDR' => '1.1.1.1']);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('test');
        if ($user === null) {
            $this->fail('No user found with username "test"');
        }

        $client->loginUser($user);
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
        $this->assertResponseStatusCodeSame(422);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => '',
            'order[note]'      => '+ Pommes', ]);
        $this->assertResponseStatusCodeSame(422);
        ClockMock::withClockMock(false);
    }

    public function testOrderingUnauthenticated(): void
    {
        $client = static::createClient([], ['REMOTE_ADDR' => '1.1.1.1']);

        $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseStatusCodeSame(401);
    }
}
