<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HolidaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HolidaysTable Test Case
 */
class HolidaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HolidaysTable
     */
    protected $Holidays;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Holidays',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Holidays') ? [] : ['className' => HolidaysTable::class];
        $this->Holidays = $this->getTableLocator()->get('Holidays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Holidays);

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
