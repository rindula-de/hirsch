<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PwaTest extends WebTestCase
{
    public function testManifest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/manifest.json');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHasHeader('content-type', 'application/manifest+json');
        $this->assertIsString($client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent() ?: '');
        // get json to variable
        /** @var array<string, string> */
        $json = json_decode($client->getResponse()->getContent() ?: '', true);
        $this->assertEquals('de-DE', $json['lang']);
        $this->assertEquals('Hirsch Bestellung', $json['short_name']);
        $this->assertEquals('/karte', $json['start_url']);
        $this->assertEquals('#ffa303', $json['theme_color']);
    }
}
