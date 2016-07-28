<?php

namespace Cradle\Http;

use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:01.
 */
class Cradle_Http_DispatcherTrait_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var DispatcherTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DispatcherTraitStub;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Http\DispatcherTrait::getDispatcher
     * @todo   Implement testGetDispatcher().
     */
    public function testGetDispatcher()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\DispatcherTrait::setDispatcher
     * @todo   Implement testSetDispatcher().
     */
    public function testSetDispatcher()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}

if(!class_exists('Cradle\Http\DispatcherTraitStub')) {
	class DispatcherTraitStub
	{
		use DispatcherTrait;
	}
}