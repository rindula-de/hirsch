<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use App\Controller\PwaController;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PwaTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ClockMock::register(PwaController::class);
        // clear cache
        $cache = new FilesystemAdapter();
        $cache->clear();
    }

    public function testManifest(): void
    {
        ClockMock::withClockMock(strtotime('12:10'));
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
        $content = file_get_contents(__DIR__.'/../../assets/styles/material_design/theme.css');

        try { // clear file
            file_put_contents(__DIR__.'/../../assets/styles/material_design/theme.css', '');
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
            file_put_contents(__DIR__.'/../../assets/styles/material_design/theme.css', $content);
        }
        ClockMock::withClockMock(false);
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
