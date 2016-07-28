<?php

namespace Cradle\Http\Response;

use PHPUnit_Framework_TestCase;
use Cradle\Data\Registry;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-28 at 11:36:34.
 */
class Cradle_Http_Response_RestTrait_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var RestTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RestTraitStub;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Http\Response\RestTrait::addValidation
     * @todo   Implement testAddValidation().
     */
    public function testAddValidation()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\Response\RestTrait::getResults
     * @todo   Implement testGetResults().
     */
    public function testGetResults()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\Response\RestTrait::getValidation
     * @todo   Implement testGetValidation().
     */
    public function testGetValidation()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\Response\RestTrait::setError
     * @todo   Implement testSetError().
     */
    public function testSetError()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Http\Response\RestTrait::setResults
     * @todo   Implement testSetResults().
     */
    public function testSetResults()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}

if(!class_exists('Cradle\Http\Request\RestTraitStub')) {
	class RestTraitStub extends Registry
	{
		use RestTrait;
	}
}
