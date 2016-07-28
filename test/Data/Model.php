<?php

namespace Cradle\Data;

use StdClass;
use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:00.
 */
class Cradle_Data_Model_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Model([
			'post_id' => 1,
			'post_title' => 'Foobar 1',
			'post_detail' => 'foobar 1',
			'post_active' => 1
		]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Cradle\Data\Model::__call
     */
    public function test__call()
    {
        $instance = $this->object->__call('setPostTitle', array('Foobar 4'));
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
		
        $actual = $this->object->__call('getPostTitle', array());
		
		$this->assertEquals('Foobar 4', $actual);
    }

    /**
     * @covers Cradle\Data\Model::get
     */
    public function testGet()
    {
        $actual = $this->object->post_title;
		
		$this->assertEquals('Foobar 1', $actual);
    }

    /**
     * @covers Cradle\Data\Model::set
     */
    public function testSet()
    {
        $this->object->post_title = 'Foobar 4';
        $actual = $this->object->post_title;
		
		$this->assertEquals('Foobar 4', $actual);
    }

    /**
     * @covers Cradle\Data\Model::offsetExists
     */
    public function testOffsetExists()
    {
        
        $this->assertTrue($this->object->offsetExists('post_id'));
        $this->assertFalse($this->object->offsetExists(3));
    }

    /**
     * @covers Cradle\Data\Model::offsetGet
     */
    public function testOffsetGet()
    {
        $actual = $this->object->offsetGet('post_id');
		$this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Data\Model::offsetSet
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet('post_id', 2);
		
		$this->assertEquals(2, $this->object['post_id']);
    }

    /**
     * @covers Cradle\Data\Model::offsetUnset
     */
    public function testOffsetUnset()
    {
		$this->object->offsetUnset('post_id');
		$this->assertFalse(isset($this->object['post_id']));
    }

    /**
     * @covers Cradle\Data\Model::current
     */
    public function testCurrent()
    {
        $actual = $this->object->current();
    	$this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Data\Model::key
     */
    public function testKey()
    {
        $actual = $this->object->key();
    	$this->assertEquals('post_id', $actual);
    }

    /**
     * @covers Cradle\Data\Model::next
     */
    public function testNext()
    {
		$this->object->next();
        $actual = $this->object->current();
    	$this->assertEquals('Foobar 1', $actual);
    }

    /**
     * @covers Cradle\Data\Model::rewind
     */
    public function testRewind()
    {
		$this->object->rewind();
        $actual = $this->object->current();
    	$this->assertEquals(1, $actual);
    }

    /**
     * @covers Cradle\Data\Model::valid
     */
    public function testValid()
    {
        $this->assertTrue($this->object->valid());
    }

    /**
     * @covers Cradle\Data\Model::count
     */
    public function testCount()
    {
        $this->assertEquals(4, count($this->object));
    }

    /**
     * @covers Cradle\Data\Model::getDot
     */
    public function testGetDot()
    {
        $this->assertEquals(1, $this->object->getDot('post_id'));
    }

    /**
     * @covers Cradle\Data\Model::isDot
     */
    public function testIsDot()
    {
		$this->assertTrue($this->object->isDot('post_id'));
    }

    /**
     * @covers Cradle\Data\Model::removeDot
     */
    public function testRemoveDot()
    {
		$this->object->removeDot('post_id');
		$this->assertFalse($this->object->isDot('post_id'));
    }

    /**
     * @covers Cradle\Data\Model::setDot
     */
    public function testSetDot()
    {
		$this->object->setDot('post_id', 2);
        $this->assertEquals(2, $this->object->getDot('post_id'));
    }

    /**
     * @covers Cradle\Data\Model::__callData
     */
    public function test__callData()
    {
        $instance = $this->object->__call('setPostTitle', array('Foobar 4'));
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
		
        $actual = $this->object->__call('getPostTitle', array());
		
		$this->assertEquals('Foobar 4', $actual);
    }

    /**
     * @covers Cradle\Data\Model::__get
     */
    public function test__get()
    {
        $actual = $this->object->post_title;
		$this->assertEquals('Foobar 1', $actual);
    }

    /**
     * @covers Cradle\Data\Model::__getData
     */
    public function test__getData()
    {
        $actual = $this->object->post_title;
		$this->assertEquals('Foobar 1', $actual);
    }

    /**
     * @covers Cradle\Data\Model::__set
     * @todo   Implement test__set().
     */
    public function test__set()
    {
        $this->object->post_title = 'Foobar 4';
        $actual = $this->object->post_title;
		
		$this->assertEquals('Foobar 4', $actual);
    }

    /**
     * @covers Cradle\Data\Model::__setData
     * @todo   Implement test__setData().
     */
    public function test__setData()
    {
        $this->object->post_title = 'Foobar 4';
        $actual = $this->object->post_title;
		
		$this->assertEquals('Foobar 4', $actual);
    }

    /**
     * @covers Cradle\Data\Model::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        $this->assertEquals(json_encode([
			'post_id' => 1,
			'post_title' => 'Foobar 1',
			'post_detail' => 'foobar 1',
			'post_active' => 1
		], JSON_PRETTY_PRINT), (string) $this->object);
    }

    /**
     * @covers Cradle\Data\Model::__toStringData
     */
    public function test__toStringData()
    {
        $this->assertEquals(json_encode([
			'post_id' => 1,
			'post_title' => 'Foobar 1',
			'post_detail' => 'foobar 1',
			'post_active' => 1
		], JSON_PRETTY_PRINT), (string) $this->object);
    }

    /**
     * @covers Cradle\Data\Model::generator
     */
    public function testGenerator()
    {
        foreach($this->object->generator() as $i => $value);
		
		$this->assertEquals('post_active', $i);
    }

    /**
     * @covers Cradle\Data\Model::getEventHandler
     */
    public function testGetEventHandler()
    {
		$instance = $this->object->getEventHandler();
		$this->assertInstanceOf('Cradle\Event\EventHandler', $instance);
    }

    /**
     * @covers Cradle\Data\Model::on
     */
    public function testOn()
    {
        $trigger = new StdClass();
		$trigger->success = null;
		
        $callback = function() use ($trigger) {
			$trigger->success = true;
		};
		
		$instance = $this
			->object
			->on('foobar', $callback)
			->trigger('foobar');
		
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
		$this->assertTrue($trigger->success);
    }

    /**
     * @covers Cradle\Data\Model::setEventHandler
     */
    public function testSetEventHandler()
    {
        $instance = $this->object->setEventHandler(new \Cradle\Event\EventHandler);
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
    }

    /**
     * @covers Cradle\Data\Model::trigger
     */
    public function testTrigger()
    {
        $trigger = new StdClass();
		$trigger->success = null;
		
        $callback = function() use ($trigger) {
			$trigger->success = true;
		};
		
		$instance = $this
			->object
			->on('foobar', $callback)
			->trigger('foobar');
		
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
		$this->assertTrue($trigger->success);
    }

    /**
     * @covers Cradle\Data\Model::i
     */
    public function testI()
    {
        $instance1 = Model::i();
		$this->assertInstanceOf('Cradle\Data\Model', $instance1);
		
		$instance2 = Model::i();
		$this->assertTrue($instance1 !== $instance2);
    }

    /**
     * @covers Cradle\Data\Model::loop
     */
    public function testLoop()
    {
        $self = $this;
        $this->object->loop(function($i) use ($self) {
            $self->assertInstanceOf('Cradle\Data\Model', $this);
            
            if ($i == 2) {
                return false;
            }
        });
    }

    /**
     * @covers Cradle\Data\Model::when
     */
    public function testWhen()
    {
        $self = $this;
        $test = 'Good';
        $this->object->when(function() use ($self) {
            $self->assertInstanceOf('Cradle\Data\Model', $this);
            return false;
        }, function() use ($self, &$test) {
            $self->assertInstanceOf('Cradle\Data\Model', $this);
            $test = 'Bad';
        });
        
        $this->assertSame('Good', $test);
    }

    /**
     * @covers Cradle\Data\Model::getInspectorHandler
     */
    public function testGetInspectorHandler()
    {
        $instance = $this->object->getInspectorHandler();
		$this->assertInstanceOf('Cradle\Profiler\InspectorInterface', $instance);
    }

    /**
     * @covers Cradle\Data\Model::inspect
     */
    public function testInspect()
    {
        ob_start();
		$this->object->inspect('foobar');
		$contents = ob_get_contents();
		ob_end_clean();  
		
		$this->assertEquals(
			'<pre>INSPECTING Variable:</pre><pre>foobar</pre>', 
			$contents
		);
    }

    /**
     * @covers Cradle\Data\Model::setInspectorHandler
     */
    public function testSetInspectorHandler()
    {
        $instance = $this->object->setInspectorHandler(new \Cradle\Profiler\InspectorHandler);
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
    }

    /**
     * @covers Cradle\Data\Model::addLogger
     */
    public function testAddLogger()
    {
        $instance = $this->object->addLogger(function() {});
		$this->assertInstanceOf('Cradle\Data\Model', $instance);
    }

    /**
     * @covers Cradle\Data\Model::log
     */
    public function testLog()
    {
		$trigger = new StdClass();
		$trigger->success = null;
        $this->object->addLogger(function($trigger) {
			$trigger->success = true;
		})
		->log($trigger);
		
		
		$this->assertTrue($trigger->success);
    }

    /**
     * @covers Cradle\Data\Model::loadState
     * @todo   Implement testLoadState().
     */
    public function testLoadState()
    {
        $state1 = new Model(array());
		$state2 = new Model(array());
		
		$state1->saveState('state1');
		$state2->saveState('state2');
		
		$this->assertTrue($state2 === $state1->loadState('state2'));
		$this->assertTrue($state1 === $state2->loadState('state1'));
    }

    /**
     * @covers Cradle\Data\Model::saveState
     */
    public function testSaveState()
    {
		$state1 = new Model(array());
		$state2 = new Model(array());
		
		$state1->saveState('state1');
		$state2->saveState('state2');
		
		$this->assertTrue($state2 === $state1->loadState('state2'));
		$this->assertTrue($state1 === $state2->loadState('state1'));
    }

    /**
     * @covers Cradle\Data\Model::__callResolver
     */
    public function test__callResolver()
    {
        $actual = $this->object->addResolver(Model::class, function() {});
		$this->assertInstanceOf('Cradle\Data\Model', $actual);
    }

    /**
     * @covers Cradle\Data\Model::addResolver
     */
    public function testAddResolver()
    {
        $actual = $this->object->addResolver(Model::class, function() {});
		$this->assertInstanceOf('Cradle\Data\Model', $actual);
    }

    /**
     * @covers Cradle\Data\Model::getResolverHandler
     */
    public function testGetResolverHandler()
    {
        $actual = $this->object->getResolverHandler();
		$this->assertInstanceOf('Cradle\Resolver\ResolverInterface', $actual);
    }

    /**
     * @covers Cradle\Data\Model::resolve
     */
    public function testResolve()
    {
        $actual = $this->object->addResolver(
			ResolverCallStub::class, 
			function() {
				return new ResolverAddStub();
			}
		)
		->resolve(ResolverCallStub::class)
		->foo('bar');
		
        $this->assertEquals('barfoo', $actual);
    }

    /**
     * @covers Cradle\Data\Model::resolveShared
     */
    public function testResolveShared()
    {
        $actual = $this
			->object
			->resolveShared(ResolverSharedStub::class)
			->reset()
			->foo('bar');
		
        $this->assertEquals('barfoo', $actual);
		
		$actual = $this
			->object
			->resolveShared(ResolverSharedStub::class)
			->foo('bar');
		
        $this->assertEquals('barbar', $actual);
    }

    /**
     * @covers Cradle\Data\Model::resolveStatic
     */
    public function testResolveStatic()
    {
        $actual = $this
			->object
			->resolveStatic(
				ResolverStaticStub::class, 
				'foo', 
				'bar'
			);
		
        $this->assertEquals('barfoo', $actual);
    }

    /**
     * @covers Cradle\Data\Model::setResolverHandler
     */
    public function testSetResolverHandler()
    {
        $actual = $this->object->setResolverHandler(new \Cradle\Resolver\ResolverHandler);
		$this->assertInstanceOf('Cradle\Data\Model', $actual);
    }
}

if(!class_exists('Cradle\Data\ResolverCallStub')) {
	class ResolverCallStub
	{
		public function foo($string)
		{
			return $string . 'foo';
		}
	}
}

if(!class_exists('Cradle\Data\ResolverAddStub')) {
	class ResolverAddStub
	{
		public function foo($string)
		{
			return $string . 'foo';
		}
	}
}

if(!class_exists('Cradle\Data\ResolverSharedStub')) {
	class ResolverSharedStub
	{
		public $name = 'foo';
		
		public function foo($string)
		{
			$name = $this->name;
			$this->name = $string;
			return $string . $name;
		}
		
		public function reset()
		{
			$this->name = 'foo';
			return $this;
		}
	}
}

if(!class_exists('Cradle\Data\ResolverStaticStub')) {
	class ResolverStaticStub
	{
		public static function foo($string)
		{
			return $string . 'foo';
		}
	}
}
