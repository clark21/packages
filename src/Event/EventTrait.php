<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Event;

use Cradle\Helper\BinderTrait;

/**
 *
 * @package  Cradle
 * @category Event
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait EventTrait
{
	use BinderTrait;

	/**
	 * @var EventHandler|null $eventHandler
	 */
	private static $eventHandler = null;
	
	/**
	 * Returns an EventHandler object
	 * if none was set, it will auto create one
	 *
	 * @return EventHandler
	 */
	public function getEventHandler()
	{
		if(is_null(self::$eventHandler)) {
			self::$eventHandler = EventHandler::i();	
		}
		
		return self::$eventHandler;
	}

    /**
     * Attaches an instance to be notified
     * when an event has been triggered
     *
     * @param *string   $event     the name of the event
     * @param *callable $callback  the event handler
     * @param int       $priority  if true will be prepended in order
     *
     * @return EventTrait
     */
    public function on($event, $callback, $priority = 0)
    {
		$dispatcher = $this->getEventHandler();

        //if it's a closure, they meant to bind the callback
        if ($callback instanceof Closure) {
            //so there's no scope
			$callback = $this->bindCallback($callback);
        }
        
		$dispatcher->on($event, $callback, $priority);

        return $this;
    }
	
	/**
	 * Allow for a custom dispatcher to be used 
	 *
	 * @param *EventHandler $handler
	 * 
	 * @return EventTrait
	 */
	public function setEventHandler(EventInterface $handler)
	{
		self::$eventHandler = $handler;
		return $this;
	}
	
	/**
     * Notify all observers of that a specific
     * event has happened
     *
     * @param string|null      $event the event to trigger
     * @param mixed[, mixed..] $arg   the arguments to pass to the handler
     *
     * @return EventTrait
     */
    public function trigger($event = null, ...$args)
    {
		$this->getEventHandler()->trigger($event, ...$args);
        return $this;
    }
}
