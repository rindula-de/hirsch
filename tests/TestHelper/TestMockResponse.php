<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\TestHelper;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class TestMockResponse extends MockResponse
{
    private string $body;

    /**
     * TestMockResponse constructor.
     *
     * @param array<string,mixed> $info
     */
    public function __construct(string $body = '', array $info = [])
    {
        parent::__construct($body, $info);
        $this->body = $body;
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     *
     * @return array<string,mixed>;
     */
    public function toArray(bool $throw = true): array
    {
        $content = json_decode($this->body);
        if (!is_array($content)) {
            throw new \Exception('Can not convert content to array');
        }
        foreach ($content as $key => $value) {
            $content[$key] = $this->objectToArray($value);
        }

        return $content;
    }

    /**
     * @return array<string,mixed>
     */
    private function objectToArray(\stdClass $object): array
    {
        $object = (array) $object;
        foreach ($object as $key => $value) {
            if ($value instanceof \stdClass) {
                $object[$key] = $this->objectToArray($value);
            }
        }

        return $object;
    }
}
