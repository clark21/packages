<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Event;

use Closure;

/**
 *
 * @vendor   Cradle
 * @package  Pipe
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait PipeTrait
{	
	use EventTrait { EventTrait::trigger as triggerEvent; }

	/**
	 * @var array $protocols Custom protocol callbacks
	 */
	protected $protocols = array();

	/**
	 * Sets up a process flow
	 *
	 * @param *string               $name     The name of this rule
	 * @param [string|callable, ..] $flow  An event name or callback
	 *
	 * @return EventPipe
	 */
	public function flow($event, ...$flow)
	{	
		//listen for the main
		$this->on($event, function(...$args) use (&$flow, $event) {
			foreach($flow as $i => $step) {
				//subflows will trigger separately
				if(is_array($step)) {
					continue;
				}

				//rule the subtasks first
				$j = 1;

				while(isset($flow[$i + $j]) && is_array($flow[$i + $j])) {
					$this->flow(...$flow[$i + $j]);
					$j++;
				}

				$this->trigger($step, ...$args);
			}
		});

		return $this;		
	}
	
	/**
	 * Adds a protocol used to custom parse an event name
	 *
	 * @param *string   $name The middleware handler
	 * @param *callable $callback The middleware handler
	 *
	 * @return Base
	 */
	public function protocol($name, $callback)
	{	
		//create a space
		$this->protocols[$name] = $callback;
		
		return $this;
	}

	/**
	 * Calls an event considering classes and protocols
	 *
	 * @param *string|callable $name
	 * @param mixed            $args
	 * 
	 * @return EventPipe
	 */
	public function trigger($name, ...$args)
	{
		//we should deal with strings 
		//then callables respectively
		//to allow overriding
		if(is_string($name)) {
			//is it a protocol?
			if(strpos($name, '://') !== false) {
				return $this->triggerProtocol($name, ...$args);
			}
	
			//they can call a class
			if(strpos($name, '@') !== false) {
				$this->triggerController($name, ...$args);
			}
			
			return $this->triggerEvent($name, ...$args);
		} 
		
		if(is_callable($name)) {
			call_user_func_array($name, $args);
		}
		
		//we can only deal with callable and strings
		//we don't want to throw an error 
		//because it could just be a pseudo
		//placeholder
		return $this;
	}
	
	/**
	 * Calls a controller method
	 *
	 * @param *string $controller In the form of class@method
	 * @param mixed   $args
	 * 
	 * @return EventPipe
	 */
	public function triggerController($controller, ...$args)
	{
		//extract the class and method
		list($class, $method) = explode('@', $controller, 2);
		
		//if the class exists
		if(class_exists($class)) {
			//instantiate it
			$instance = new $class();
			
			//does the method exist ?
			if(method_exists($instance, $method)) {
				call_user_func_array([$instance, $method], $args);
			}
		}
		
		return $this;
	}
	
	/**
	 * Calls a protocol
	 *
	 * @param *string $protocol In the form of protocol://event
	 * @param *Event  $event    Event object to pass
	 * 
	 * @return Base
	 */
	public function triggerProtocol($protocol, ...$args)
	{
		list($protocol, $name) = explode('://', $protocol, 2);
		
		//if it's not a registered protocol
		if(!isset($this->protocols[$protocol])) {
			//oops?
			return $this;
		}

		//get the protocol
		$protocol = $this->protocols[$protocol];
		
		//we should deal with strings 
		//then callables respectively
		//to allow overriding
		
		//they can call a class
		if(is_string($protocol) && strpos($protocol, '@') !== false) {
			return $this->triggerController($protocol, $name, ...$args);
		}
		
		//late binding ?
		if($protocol instanceof Closure) {
			$protocol = $this->bindCallback($protocol);
		}

		if(is_callable($protocol)) {
			//call the protocol
			call_user_func($protocol, $name, ...$args);
		}
		
		//we can only deal with callable and strings
		//we don't want to throw an error 
		//because it could just be a pseudo
		//placeholder
		return $this;
	}
}
