<?php


namespace App\Tests\TestHelper;

use Infection\FileSystem\Locator\FileNotFound;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class TestCurlHttpClient implements HttpClientInterface
{
    private string $projectDir;
    private string $prefixUrl;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container,string $prefixUrl)
    {
        $this->projectDir = $container->get('kernel')->getProjectDir();
        $this->prefixUrl = $prefixUrl;
    }

    /**
     * @param array<string,mixed> $options
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $pathToMockResponse = $this->projectDir."/tests/mockedApiRequestResponse/";
        $url = str_replace($this->prefixUrl,$pathToMockResponse,$url);
        $url = str_replace("?",".",$url);
        $content = file_get_contents($url);
        if(!$content)throw new FileNotFound();
        return new TestMockResponse($content);
    }

    public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
    {
        throw new NotImplementedException("Not Implemented");
    }

    /**
     * @param array<string,mixed> $options
     * @return $this
     */
    public function withOptions(array $options): static
    {
        throw new NotImplementedException("Not Implemented");
    }
}
