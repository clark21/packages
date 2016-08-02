<?php

namespace Cradle\Http\Request;

use PHPUnit_Framework_TestCase;
use Cradle\Data\Registry;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-28 at 11:36:34.
 */
class Cradle_Http_Request_ContentTrait_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTrait
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ContentTraitStub;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * covers Cradle\Http\Request\ContentTrait::getContent
     */
    public function testGetContent()
    {
        $this->object->set('body', 'foobar');	
		$actual = $this->object->getContent();
		$this->assertEquals('foobar', $actual);
    }

    /**
     * covers Cradle\Http\Request\ContentTrait::hasContent
     */
    public function testHasContent()
    {
		$this->assertFalse($this->object->hasContent());
		$this->object->set('body', 'foobar');
		$this->assertTrue($this->object->hasContent());
    }

    /**
     * covers Cradle\Http\Request\ContentTrait::setContent
     */
    public function testSetContent()
    {
		$instance = $this->object->setContent('foobar');
		
		$this->assertInstanceOf('Cradle\Http\Request\ContentTraitStub', $instance);
    }
}

if(!class_exists('Cradle\Http\Request\ContentTraitStub')) {
	class ContentTraitStub extends Registry
	{
		use ContentTrait;
	}
}
