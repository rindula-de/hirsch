<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\HirschController;
use App\Model\Table\OrdersTable;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\HirschController Test Case
 *
 * @uses \App\Controller\HirschController
 */
class HirschControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @var OrdersTable $Orders
     */
    private $Orders;

    public function setUp(): void
    {
        parent::setUp();
        $this->Orders = $this->getTableLocator()->get('orders', ['contains' => [
            'paypalmes'
        ]]);
    }

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Paypalmes',
        'app.Orders',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get(['controller' => 'hirsch', 'action' => 'index']);
        $this->assertResponseOk();
    }

    /**
     * Test order method
     *
     * @return void
     */
    public function testOrder(): void
    {
        debug($this->Orders);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $order = $this->Orders->get(1);
        $this->assertTextEquals('Extra Mayonese', $order->note);
        $this->get(['controller' => 'hirsch', 'action' => 'order']);
        $this->assertRedirect(['action' => 'index']);
        $this->post(['controller' => 'hirsch', 'action' => 'order']);
        $this->assertRedirect(['action' => 'index']);
        $this->post(['controller' => 'hirsch', 'action' => 'order', 0, 'Cordonblöh'], []);
        $this->assertNoRedirect();
        $this->assertResponseOk();
        $this->post(['controller' => 'hirsch', 'action' => 'order', 0, 'Cordonblöh'], ['name' => 'Cordonblöh', 'paypalme' => 1, 'note' => 'Mit Pommäs']);
        $this->assertRedirect("https://paypal.me/rindulalp/3.5");
        $this->post(['controller' => 'hirsch', 'action' => 'order', 2, 'Pizza'], ['name' => 'Pizza', 'paypalme' => 1, 'note' => 'Hawaii']);
        $this->assertRedirect("https://paypal.me/rindulalp/3.5");
        $this->post(['controller' => 'hirsch', 'action' => 'order', 1, 'Pizza'], ['name' => 'Pizza', 'paypalme' => 1]);
        $this->assertRedirect("https://paypal.me/rindulalp/3.5");
    }

    /**
     * Test orders method
     *
     * @return void
     */
    public function testOrders(): void
    {
        $this->get(['controller' => 'hirsch', 'action' => 'orders']);
        $this->assertResponseOk();
    }
}
