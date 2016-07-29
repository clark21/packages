<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Event;

use Cradle\Helper\InstanceTrait;
use Cradle\Resolver\ResolverTrait;

/**
 * Allows the ability to listen to events made known by another
 * piece of functionality. Events are items that transpire based
 * on an action. With events you can add extra functionality
 * right after the event has triggered.
 *
 * @package  Cradle
 * @category Event
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class EventHandler implements EventInterface
{
	use ResolverTrait, InstanceTrait;

	 /**
     * @var array $observers cache of event handlers
     */
    protected static $observers = [];
    
    /**
     * Stops listening to an event
     *
     * @param string|null   $event    name of the event
     * @param callable|null $callback callback handler
     *
     * @return EventHandler
     */
    public function off($event = null, $callback = null)
    {
		//if it's not callable
		if(!is_callable($callback)) {
			//set it to null
			$callback = null;
		}

        //if there is no event and not callable
        if (is_null($event) && is_null($callback)) {
            //it means that they want to remove everything
            self::$observers = [];
            return $this;
        }
		
		//if there are callbacks listening to
		//this and no callback was specified
		if(isset(self::$observers[$event]) && is_null($callback)) {
			//it means that they want to remove 
			//all callbacks listening to this event
			unset(self::$observers[$event]);
			return $this;
		}
		
		//if there are callbacks listening 
		//to this and we have a callback
		if(isset(self::$observers[$event]) && is_callable($callback)) {
			return $this->removeObserversByEvent($event, $callback);
		}
		
		//if no event, but there is a callback
		if(is_null($event) && is_callable($callback)) {
			return $this->removeObserversByCallback($callback);
		}

        return $this;
    }
     
    /**
     * Attaches an instance to be notified
     * when an event has been triggered
     *
     * @param *string   $event    The name of the event
     * @param *callable $callback The event handler
     * @param int       $priority Set the importance
     *
     * @return EventHandler
     */
    public function on($event, $callback, $priority = 0)
    {
        //set up the observer
		$observer = $this->resolve(EventObserver::class, $callback);
        
		self::$observers[$event][$priority][] = $observer;
		
        return $this;
    }

    /**
     * Notify all observers of that a specific
     * event has happened
     *
     * @param *string $event The event to trigger
     * @param mixed   ...$args The arguments to pass to the handler
     *
     * @return EventHandler
     */
    public function trigger($event, ...$args)
    {
		if(!isset(self::$observers[$event])) {
			return $this;
		}
        
		krsort(self::$observers[$event]);
        $observers = call_user_func_array('array_merge', self::$observers[$event]);

        //for each observer
        foreach ($observers as $observer) {
			$callback = $observer->getCallback();
            //if this is the same event, call the method, if the method returns false
            if (call_user_func_array($callback, $args) === false) {
                //break out of the loop
                break;
            }
        }

        return $this;
    }
	
	/**
	 * Removes all observers matching this callback
	 *
	 * @param *callable $callback
	 *
	 * @return EventHandler
	 */
	protected function removeObserversByCallback($callback)
	{
		//find the callback
		foreach(self::$observers as $event => $priorities) {
			$this->removeObserversByEvent($event, $callback);
		}
		
		return $this;
	}
	
	/**
	 * Removes all observers matching this event and callback
	 *
	 * @param *string   $event
	 * @param *callable $callback
	 *
	 * @return EventHandler
	 */
	protected function removeObserversByEvent($event, $callback)
	{
		//if event isn't set
		if(!isset(self::$observers[$event])) {
			//do nothing
			return $this;
		}

		//'foobar' => array(
		foreach(self::$observers[$event] as $priority => $observers) {
			//0 => array(
			foreach($observers as $i => $observer) {
				//0 => callback
				if($observer->assertEquals($callback)) {
					unset(self::$observers[$priority][$i]);
				}
			}
		}
		
		return $this;
	}
}