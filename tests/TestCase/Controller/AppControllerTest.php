<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AppController Test Case
 *
 * @uses \App\Controller\AppController
 */
class AppControllerTest extends TestCase
{
    use IntegrationTestTrait;


    protected $fixtures = [
        "app.Hirsch",
        "app.Holidays",
    ];


    /**
     * Test e404 method
     *
     * @return void
     */
    public function testE404(): void
    {
        $this->get("/aaaa");
        $this->assertRedirect("/karte");
        $this->get("/gswsg");
        $this->assertRedirect("/karte");
        $this->get("/karte");
        $this->assertResponseSuccess();
    }
}
