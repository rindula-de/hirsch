<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PaypalmesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PaypalmesTable Test Case
 */
class PaypalmesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PaypalmesTable
     */
    protected $Paypalmes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Paypalmes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Paypalmes') ? [] : ['className' => PaypalmesTable::class];
        $this->Paypalmes = TableRegistry::getTableLocator()->get('Paypalmes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Paypalmes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
