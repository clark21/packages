<?php

namespace Cradle\Sql\PostGreSql;

use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-30 at 04:38:37.
 */
class Cradle_Sql_PostGreSql_QueryAlter_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var QueryAlter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QueryAlter('foobar');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::addField
     */
    public function testAddField()
    {
        $instance = $this->object->addField('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::addPrimaryKey
     */
    public function testAddPrimaryKey()
    {
        $instance = $this->object->addField('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::changeField
     */
    public function testChangeField()
    {
        $instance = $this->object->changeField('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::getQuery
     */
    public function testGetQuery()
    {
        $actual = $this->object->getQuery();
		$this->assertEquals('ALTER TABLE "foobar" ;', $actual);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::removeField
     */
    public function testRemoveField()
    {
        $instance = $this->object->removeField('foobar');
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::removePrimaryKey
     */
    public function testRemovePrimaryKey()
    {
        $instance = $this->object->removePrimaryKey('foobar');
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }

    /**
     * @covers Cradle\Sql\PostGreSql\QueryAlter::setName
     */
    public function testSetName()
    {
        $instance = $this->object->setName('foobar');
		$this->assertInstanceOf('Cradle\Sql\PostGreSql\QueryAlter', $instance);
    }
}