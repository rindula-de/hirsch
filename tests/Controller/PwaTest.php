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

        // check if all required keys are set
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('short_name', $json);
        $this->assertArrayHasKey('start_url', $json);
        $this->assertArrayHasKey('theme_color', $json);
        $this->assertArrayHasKey('background_color', $json);
        $this->assertArrayHasKey('display', $json);
        $this->assertArrayHasKey('orientation', $json);
        $this->assertArrayHasKey('icons', $json);
        $this->assertArrayHasKey('lang', $json);

        // Second request if app.scss is wrong formatted
        $content = file_get_contents(__DIR__ . '/../../assets/styles/app.scss');
        try { // clear file
            file_put_contents(__DIR__ . '/../../assets/styles/app.scss', '');
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
            $this->assertEquals('#3f51b5', $json['theme_color']);

            // check if all required keys are set
            $this->assertArrayHasKey('name', $json);
            $this->assertArrayHasKey('short_name', $json);
            $this->assertArrayHasKey('start_url', $json);
            $this->assertArrayHasKey('theme_color', $json);
            $this->assertArrayHasKey('background_color', $json);
            $this->assertArrayHasKey('display', $json);
            $this->assertArrayHasKey('orientation', $json);
            $this->assertArrayHasKey('icons', $json);
            $this->assertArrayHasKey('lang', $json);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            // restore file
            file_put_contents(__DIR__ . '/../../assets/styles/app.scss', $content);
        }
    }

    public function testServiceWorker(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sw.js');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHasHeader('content-type', 'application/javascript');
        $this->assertIsString($client->getResponse()->getContent());
    }
}
