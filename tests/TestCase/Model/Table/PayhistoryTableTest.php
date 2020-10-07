<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PayhistoryTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PayhistoryTable Test Case
 */
class PayhistoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var PayhistoryTable
     */
    protected $Payhistory;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Payhistory',
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
        $config = $this->getTableLocator()->exists('Payhistory') ? [] : ['className' => PayhistoryTable::class];
        $this->Payhistory = $this->getTableLocator()->get('Payhistory', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Payhistory);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
