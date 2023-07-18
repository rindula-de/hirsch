<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Tests\Utility;

use App\Service\UtilityService;
use PHPUnit\Framework\TestCase;

class TestUtility extends TestCase
{
    public function testHashDirectory(): void
    {
        $utility = new UtilityService();
        $hash = $utility->hashDirectory(__DIR__.'/../../src');
        $this->assertIsString($hash);
        $this->assertEquals(32, strlen($hash));

        // returns false if directory does not exist or is a file
        $this->assertFalse($utility->hashDirectory(__DIR__.'/../../src/Controller/PwaController.php'));
        $this->assertFalse($utility->hashDirectory(__DIR__.'/doesNotExist'));
    }
}
