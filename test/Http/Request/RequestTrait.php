<?php

namespace Cradle\Http;

use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:01.
 */
class Cradle_Http_RequestTrait_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var RequestTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RequestTraitStub;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Http\RequestTrait::getRequest
     * @todo   Implement testGetRequest().
     */
    public function testGetRequest()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\RequestTrait::setRequest
     * @todo   Implement testSetRequest().
     */
    public function testSetRequest()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}

if(!class_exists('Cradle\Http\RequestTraitStub')) {
	class RequestTraitStub
	{
		use RequestTrait;
	}
}