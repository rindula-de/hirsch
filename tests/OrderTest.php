<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderTest extends WebTestCase
{
    public function testOrderingAuthenticated(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('test');

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
        
        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => 'Max Mustermann',
            'order[note]'      => '', ]);
        $this->assertResponseStatusCodeSame(401);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => 'Max Mustermann',
            'order[note]'      => '+ Pommes', ]);
        $this->assertResponseStatusCodeSame(401);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => '',
            'order[note]'      => '', ]);
        $this->assertResponseStatusCodeSame(401);

        $crawler = $client->request('GET', '/order/0/Schweizer-Wurstsalat-mit-Pommes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Schweizer Wurstsalat mit Pommes');
        $form = $crawler->selectButton('order[submit]')->form();
        $client->submit($form, [
            'order[orderedby]' => '',
            'order[note]'      => '+ Pommes', ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
