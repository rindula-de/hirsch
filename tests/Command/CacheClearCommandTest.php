<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Tester\CommandTester;

class CacheClearCommandTest extends KernelTestCase
{

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testCacheCleared():void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:cache:clear');

        $cache = new FilesystemAdapter();
        $cache->get("test",function(){
            return true;
        });
        $this->assertTrue($cache->getItem("test")->get());

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Cache cleared!', $output);

        $this->assertNull($cache->getItem("test")->get());
    }
}
