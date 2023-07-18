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

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/manifest+json');
        self::assertResponseHeaderSame('cache-control', 'public');
        self::assertResponseHasHeader('etag');
        self::assertIsString($client->getResponse()->getContent());
        self::assertJson($client->getResponse()->getContent() ?: '');
        // get json to variable
        /** @var array<string, mixed> */
        $json = json_decode($client->getResponse()->getContent() ?: '', true);
        self::assertEquals('de-DE', $json['lang']);
        self::assertEquals('Hirsch Bestellung', $json['short_name']);
        self::assertEquals('/karte', $json['start_url']);
        self::assertEquals('#ffa303', $json['theme_color']);

        // check if all required keys are set
        self::assertArrayHasKey('name', $json);
        self::assertArrayHasKey('short_name', $json);
        self::assertArrayHasKey('description', $json);
        self::assertArrayHasKey('start_url', $json);
        self::assertArrayHasKey('theme_color', $json);
        self::assertArrayHasKey('background_color', $json);
        self::assertArrayHasKey('display', $json);
        self::assertArrayHasKey('orientation', $json);
        self::assertArrayHasKey('icons', $json);

        self::assertCount(1, $json['icons']);

        // check if all icons are set
        self::assertArrayHasKey('src', $json['icons'][0]);
        self::assertArrayHasKey('type', $json['icons'][0]);
        self::assertArrayHasKey('sizes', $json['icons'][0]);
        self::assertArrayHasKey('purpose', $json['icons'][0]);

        // check if all shortcuts are set
        self::assertArrayHasKey('shortcuts', $json);
        self::assertCount(1, $json['shortcuts']);
        self::assertArrayHasKey('name', $json['shortcuts'][0]);
        self::assertArrayHasKey('url', $json['shortcuts'][0]);
        self::assertArrayHasKey('description', $json['shortcuts'][0]);

        self::assertArrayHasKey('lang', $json);

        // check if the version and version_name isset correctly
        self::assertArrayHasKey('version', $json);
        self::assertEquals('test', $json['version']);
        self::assertArrayHasKey('version_name', $json);
        self::assertEquals('test', $json['version_name']);

        // check if the manifest version is set correctly
        self::assertArrayHasKey('manifest_version', $json);
        self::assertEquals(3, $json['manifest_version']);

        // check if etag works
        $etag = $client->getResponse()->headers->get('etag');
        $client->request('GET', '/manifest.json', [], [], ['HTTP_If-None-Match' => $etag]);
        self::assertResponseStatusCodeSame(304);

        ClockMock::withClockMock(false);
    }

    public function testServiceWorker(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sw.js');

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/javascript');
        self::assertResponseHeaderSame('cache-control', 'public');
        self::assertResponseHasHeader('etag');
        self::assertIsString($client->getResponse()->getContent());

        // check if etag works
        $etag = $client->getResponse()->headers->get('etag');
        $client->request('GET', '/sw.js', [], [], ['HTTP_If-None-Match' => $etag]);
        self::assertResponseStatusCodeSame(304);
    }
}
