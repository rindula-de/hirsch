<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Controller;

use App\Entity\Holidays;
use App\Tests\TestHelper\TestCurlHttpClient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\Component\Stopwatch\Stopwatch;

class ModalControllerTest extends WebTestCase
{
    private EntityManager $entityManager;
    private KernelBrowser $client;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->entityManager = $entityManager;
        $this->entityManager->beginTransaction();

        // prepare environment
        $cache = new FilesystemAdapter();
        $cache->clear();

        /** @var Stopwatch $stopwatch */
        $stopwatch = $this->getContainer()->get('debug.stopwatch');
        $httpCurlClient = new TestCurlHttpClient($this->getContainer(), "https://api.github.com/repos/Rindula/hirsch/");
        $httpClient = new TraceableHttpClient($httpCurlClient,$stopwatch);
        $this->getContainer()->set('http_client',$httpClient);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testInHolidays(): void
    {
        $holiday = new Holidays();
        $holiday->setStart(new \DateTime('-1 day'));
        $holiday->setEnd(new \DateTime('+1 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/modalInformationText');
        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($this->client->getResponse()->getContent());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testNotInHolidays(): void
    {
        $holiday = new Holidays();
        $holiday->setStart(new \DateTime('+1 day'));
        $holiday->setEnd(new \DateTime('+3 day'));
        $this->entityManager->persist($holiday);
        $this->entityManager->flush();

        $this->client->request('GET', '/modalInformationText');
        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    /**
     * @throws \Exception
     */
    public function testOneNewRelease(): void
    {
        $content = $this->callReleaseModal("v2.7.0");
        if(!$content)throw new \Exception("Not Content");

        $this->assertStringContainsString("v2.8.0",$content);
        $this->assertStringNotContainsString("v2.7.0",$content);
    }

    /**
     * @throws \Exception
     */
    public function testMultipleNewReleases(): void
    {
        $content = $this->callReleaseModal("v2.6.0");
        if(!$content)throw new \Exception("Not Content");

        $this->assertStringContainsString("v2.8.0",$content);
        $this->assertStringContainsString("v2.7.0",$content);
        $this->assertStringNotContainsString("v2.6.0",$content);
    }

    /**
     * @throws \Exception
     */
    public function testNoNewRelease():void
    {
        $content = $this->callReleaseModal("v2.8.0");

        $this->assertEmpty($content);
    }



    protected function tearDown(): void
    {
        if($this->entityManager->getConnection()->isTransactionActive())$this->entityManager->rollback();
        parent::tearDown();
    }

    /**
     * @param string $version
     * @return false|string
     * @throws \Exception
     */
    private function callReleaseModal(string $version): string|false
    {
        $_ENV['APP_VERSION'] = "v2.8.0";
        $this->client->getCookieJar()->set(new Cookie('changelogVersion', $version, httponly: false));
        $this->client->request('GET', '/modalChangelog');
        $this->assertResponseIsSuccessful();
        $cookies = $this->client->getResponse()->headers->getCookies();
        $cookieValue = null;
        foreach ($cookies as $cookie){
            if($cookie->getName()=="changelogVersion"){
                $cookieValue = $cookie->getValue();
                break;
            }
        }
        if($cookieValue === null) throw new \LogicException("Cookie not found");
        $this->assertEquals($_ENV['APP_VERSION'],$cookieValue);
        $content = file_get_contents(__DIR__ . "/../mockedApiRequestResponse/releases.page=0");
        if (!$content) throw new \Exception("Error in test");

        return $this->client->getResponse()->getContent();
    }
}
