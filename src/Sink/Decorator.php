<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace

{
	use Cradle\Sink\Decorator;

    /**
     * The starting point of every framework call.
     */
    function cradle(...$args)
    {
        $decorator = Decorator::i();

        if (func_num_args() == 0) {
            return $decorator;
        }
    	
		$callback = array_shift($args);
		
		if(is_callable($callback)) {
			$callback = $callback->bindTo($eden, get_class($eden));
			return call_user_func_array($callback, $args);
		}
		
        return $decorator->resolve($callback, ...$args);
    }
}

namespace Cradle\Sink

{
	use Cradle\Data\DataTrait;
	use Cradle\Event\EventTrait;
	
	use Cradle\Helper\SingletonTrait;
	use Cradle\Helper\BinderTrait;
	use Cradle\Helper\LoopTrait;
	use Cradle\Helper\ConditionalTrait;
	
	use Cradle\Profiler\CallerTrait;
	use Cradle\Profiler\InspectorTrait;
	use Cradle\Profiler\LoggerTrait;
	
	use Cradle\Resolver\StateTrait;
	
	/**
	 * Used to inspect classes and result sets
	 *
	 * @package  Cradle
	 * @category Decorator
	 * @author   Christian Blanquera <cblanquera@openovate.com>
	 * @standard PSR-2
	 */
	class Decorator
	{
		use EventTrait, 
			SingletonTrait, 
			BinderTrait, 
			LoopTrait, 
			ConditionalTrait, 
			CallerTrait, 
			InspectorTrait, 
			LoggerTrait, 
			StateTrait,
			DataTrait
			{
				DataTrait::__toStringData as __toString;
			}

		/**
		 * @const int DECORATE
		 */
		const DECORATE = 1;
	}
}