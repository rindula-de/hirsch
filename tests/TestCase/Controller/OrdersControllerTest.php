<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\OrdersController Test Case
 *
 * @uses \App\Controller\OrdersController
 */
class OrdersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Orders',
        'app.Hirsch',
    ];

    /**
     * Test order method
     *
     * @return void
     */
    public function testOrder(): void
    {
        Configure::write('debug', true);
        $table = $this->getTableLocator()->get('Orders');
        $this->assertEquals(1, $table->find()->count());
        $this->get(['_name' => 'bestellen', 0, 'tagesessen']);
        $this->assertResponseOk();
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen'], ['name' => 'tagesessen']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen'], ['orderedby' => 'Max Mustermann']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen'], ['note' => 'Ohne Zwiebeln']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen'], ['name' => 'tagesessen', 'orderedby' => 'Max Mustermann']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen'], ['name' => 'tagesessen', 'orderedby' => 'Max Mustermann', 'note' => 'Mal so, mal so']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen', 0, 'tagesessen'], ['name' => 'tagesessen']);
        $this->assertResponseCode(400);
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen', 0, 'tagesessen'], ['orderedby' => 'Max Mustermann']);
        $this->assertResponseFailure();
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen', 0, 'tagesessen'], ['note' => 'Ohne Zwiebeln']);
        $this->assertResponseCode(400);
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen', 0, 'tagesessen'], ['name' => 'tagesessen', 'orderedby' => 'Max Mustermann']);
        $this->assertResponseSuccess();
        $this->assertEquals(2, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['_name' => 'bestellen', 0, 'tagesessen'], ['name' => 'tagesessen', 'orderedby' => 'Max Mustermann', 'note' => 'Mal so, mal so']);
        $this->assertResponseSuccess();
        $this->assertEquals(3, $table->find()->count());
        $this->assertRedirect(['_name' => 'bezahlen']);
    }

    /**
     * Test list method
     *
     * @return void
     */
    public function testList(): void
    {
        $this->get(['_name' => 'bestellungen']);
        $this->assertResponseOk();
        $this->assertResponseContains("Sven");
        $this->assertResponseContains("Tagesessen");
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        Configure::write('debug', true);
        $table = $this->getTableLocator()->get('Orders');
        $this->assertEquals(1, $table->find()->count());
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(['controller' => 'orders', 'action' => 'delete', base64_encode($table->find()->first()->id . "")]);
        $this->assertResponseSuccess();
        $this->assertEquals(0, $table->find()->count());
    }

    /**
     * Test extend method
     *
     * @return void
     */
    public function testExtend(): void
    {
        Configure::write('debug', true);
        $extended = Cache::read("settings.extended", 'extended') ?? false;
        $this->assertEquals(false, $extended);
        $this->get(['controller' => 'Orders', 'action' => 'extend']);
        $this->assertResponseSuccess();
        $extended = Cache::read("settings.extended", 'extended') ?? false;
        $this->assertEquals(true, $extended);
    }
}
