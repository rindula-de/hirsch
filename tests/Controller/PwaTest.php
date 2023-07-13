<?php

/*
 * (c) Sven Nolting, 2023
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
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('content-type', 'application/manifest+json');
        $this->assertIsString($client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent() ?: '');
        // get json to variable
        /** @var array<string, mixed> */
        $json = json_decode($client->getResponse()->getContent() ?: '', true);
        $this->assertEquals('de-DE', $json['lang']);
        $this->assertEquals('Hirsch Bestellung', $json['short_name']);
        $this->assertEquals('/karte', $json['start_url']);
        $this->assertEquals('#ffa303', $json['theme_color']);

        // check if all required keys are set
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('short_name', $json);
        $this->assertArrayHasKey('description', $json);
        $this->assertArrayHasKey('start_url', $json);
        $this->assertArrayHasKey('theme_color', $json);
        $this->assertArrayHasKey('background_color', $json);
        $this->assertArrayHasKey('display', $json);
        $this->assertArrayHasKey('orientation', $json);
        $this->assertArrayHasKey('icons', $json);

        $this->assertCount(1, $json['icons']);

        // check if all icons are set
        $this->assertArrayHasKey('src', $json['icons'][0]);
        $this->assertArrayHasKey('type', $json['icons'][0]);
        $this->assertArrayHasKey('sizes', $json['icons'][0]);
        $this->assertArrayHasKey('purpose', $json['icons'][0]);

        // check if all shortcuts are set
        $this->assertArrayHasKey('shortcuts', $json);
        $this->assertCount(1, $json['shortcuts']);
        $this->assertArrayHasKey('name', $json['shortcuts'][0]);
        $this->assertArrayHasKey('url', $json['shortcuts'][0]);
        $this->assertArrayHasKey('description', $json['shortcuts'][0]);

        $this->assertArrayHasKey('lang', $json);

        // check if the version and version_name isset correctly
        $this->assertArrayHasKey('version', $json);
        $this->assertEquals('test', $json['version']);
        $this->assertArrayHasKey('version_name', $json);
        $this->assertEquals('test', $json['version_name']);

        // check if the manifest version is set correctly
        $this->assertArrayHasKey('manifest_version', $json);
        $this->assertEquals(3, $json['manifest_version']);

        ClockMock::withClockMock(false);
    }

    public function testServiceWorker(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sw.js');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHasHeader('content-type', 'application/javascript');
        $this->assertIsString($client->getResponse()->getContent());
    }
}
