<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\FooddisplayHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\FooddisplayHelper Test Case
 */
class FooddisplayHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\FooddisplayHelper
     */
    protected $Fooddisplay;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Fooddisplay = new FooddisplayHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Fooddisplay);

        parent::tearDown();
    }
}
