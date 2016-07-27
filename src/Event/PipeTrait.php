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
	 * Calls an event considering classes and protocols
	 *
	 * @param *string|callable $name
	 * @param mixed            $args
	 * 
	 * @return EventPipe
	 */
	public function trigger($name, ...$args)
	{
		if(is_callable($name)) {
			call_user_func_array($name, $args);
			return $this;
		}
		
		//we don't want to throw an error 
		//because it could just be a pseudo
		//placeholder
		if(!is_string($name)) {
			return $this;
		}

		//they can call a class
		if(strpos($name, '@') !== false) {
			$this->triggerController($name, ...$args);
		}
		
		return $this->triggerEvent($name, ...$args);;
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
}
