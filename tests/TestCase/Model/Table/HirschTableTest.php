<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HirschTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HirschTable Test Case
 */
class HirschTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HirschTable
     */
    protected $Hirsch;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Hirsch',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Hirsch') ? [] : ['className' => HirschTable::class];
        $this->Hirsch = TableRegistry::getTableLocator()->get('Hirsch', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Hirsch);

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
