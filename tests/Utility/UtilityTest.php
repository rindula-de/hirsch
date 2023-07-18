<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Tests\Utility;

use App\Service\UtilityService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilityTest extends WebTestCase
{
    public function testHashDirectory(): void
    {
        $utility = new UtilityService();
        $hash = $utility->hashDirectory(__DIR__.'/../../src');
        self::assertIsString($hash);
        self::assertEquals(32, strlen($hash));

        $hash = $utility->hashDirectory(__DIR__.'/../../config');
        self::assertIsString($hash);
        self::assertEquals(32, strlen($hash));

        // returns false if directory does not exist or is a file
        self::assertFalse($utility->hashDirectory(__DIR__.'/../../src/Controller/PwaController.php'));
        self::assertFalse($utility->hashDirectory(__DIR__.'/doesNotExist'));
        self::assertFalse($utility->hashDirectory(__DIR__.'/../../..'));
    }
}
