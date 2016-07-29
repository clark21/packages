<?php

namespace Cradle\Frame;

use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 13:49:45.
 */
class Cradle_Frame_PackageTrait_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var PackageTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PackageTraitStub;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Frame\PackageTrait::package
     * @todo   Implement testPackage().
     */
    public function testPackage()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Frame\PackageTrait::register
     * @todo   Implement testRegister().
     */
    public function testRegister()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Frame\PackageTrait::setBoostrapFile
     * @todo   Implement testSetBoostrapFile().
     */
    public function testSetBoostrapFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Cradle\Frame\PackageTrait::bindCallback
     * @todo   Implement testBindCallback().
     */
    public function testBindCallback()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}

if(!class_exists('Cradle\Frame\PackageTraitStub')) {
	class PackageTraitStub
	{
		use PackageTrait;
	}
}
