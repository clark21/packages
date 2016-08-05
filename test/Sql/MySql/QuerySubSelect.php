<?php

namespace Cradle\Sql\MySql;

use PHPUnit_Framework_TestCase;

use Cradle\Sql\QuerySelect;
/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:03.
 */
class Cradle_Sql_MySql_QuerySubSelect_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var QuerySubSelect
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new QuerySubSelect(new QuerySelect);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Sql\MySql\QuerySubSelect::getQuery
     */
    public function testGetQuery()
    {
        $actual = $this->object->getQuery();
		$this->assertEquals('(SELECT * FROM   )', $actual);
    }

    /**
     * @covers Cradle\Sql\MySql\QuerySubSelect::setParentQuery
     */
    public function testSetParentQuery()
    {
        $instance = $this->object->setParentQuery(new QuerySelect);
		$this->assertInstanceOf('Cradle\Sql\MySql\QuerySubSelect', $instance);
    }
}