<?php

namespace Cradle\Sql\Mysql;

use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:03.
 */
class Cradle_Sql_MySql_QueryCreate_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var QueryCreate
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QueryCreate('foobar');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::addField
     */
    public function testAddField()
    {
        $instance = $this->object->addField('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::addKey
     */
    public function testAddKey()
    {
        $instance = $this->object->addKey('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::addPrimaryKey
     */
    public function testAddPrimaryKey()
    {
        $instance = $this->object->addPrimaryKey('foobar');
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::addUniqueKey
     */
    public function testAddUniqueKey()
    {
        $instance = $this->object->addUniqueKey('foobar', array());
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::getQuery
     */
    public function testGetQuery()
    {
        $actual = $this->object->getQuery();
		$this->assertEquals('CREATE TABLE `foobar` ();', $actual);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setComments
     */
    public function testSetComments()
    {
        $instance = $this->object->setComments('foobar');
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setFields
     */
    public function testSetFields()
    {
        $instance = $this->object->setFields(array('foobar'));
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setKeys
     */
    public function testSetKeys()
    {
        $instance = $this->object->setKeys(array('foobar'));
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setName
     */
    public function testSetName()
    {
        $instance = $this->object->setName('foobar');
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setPrimaryKeys
     */
    public function testSetPrimaryKeys()
    {
        $instance = $this->object->setPrimaryKeys(array('foobar'));
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }

    /**
     * @covers Cradle\Sql\MySql\QueryCreate::setUniqueKeys
     */
    public function testSetUniqueKeys()
    {
        $instance = $this->object->setUniqueKeys(array('foobar'));
		$this->assertInstanceOf('Cradle\Sql\Mysql\QueryCreate', $instance);
    }
}
