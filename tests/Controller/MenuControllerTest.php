<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use App\Entity\Hirsch;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MenuControllerTest extends WebTestCase
{
    private EntityManager $entityManager;
    private KernelBrowser $client;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();
    }

    public function testRootPath(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/karte', 302);
    }

    public function testApiGetMenu(): void
    {
        $cache = new FilesystemAdapter();
        $cache->delete('menu_disabled');

        $this->client->request('GET', '/api/get-menu');
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        if (!$response) {
            $this->fail('Not a valid response');
        }
        $response = json_decode($response);
        if (!is_array($response)) {
            $this->fail('Not a valid response');
        }
        $this->assertGreaterThan(0, count($response));
        $wurstsalat = array_filter($response, function ($value) {
            $value = get_object_vars($value);
            if (!is_array($value)) {
                return false;
            }
            if ('Schweizer Wurstsalat mit Pommes' == $value['name']) {
                return true;
            }

            return false;
        });
        if (!is_array($wurstsalat)) {
            $this->fail('Missing wurstsalat');
        }
        $wurstsalat = get_object_vars($wurstsalat[0]);
        $this->assertEquals([
            'id' => 2,
            'slug' => 'Schweizer-Wurstsalat-mit-Pommes',
            'name' => 'Schweizer Wurstsalat mit Pommes',
            'display' => true,
        ], $wurstsalat);
    }

    public function testShowNoMenu(): void
    {
        $this->entityManager->createQueryBuilder()
            ->update(Hirsch::class, 'h')
            ->set('h.display', '0')
            ->getQuery()
            ->execute();
        $this->client->request('GET', '/api/get-menu');
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        if (!$response) {
            $this->fail('Not a valid response');
        }
        $response = json_decode($response);
        if (!is_array($response)) {
            $this->fail('Not a valid response');
        }
        $this->assertCount(0, $response);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        parent::tearDown();
    }
}
