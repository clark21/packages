<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Frame;

use Cradle\Helper\InstanceTrait;
use Cradle\Helper\LoopTrait;
use Cradle\Helper\ConditionalTrait;

use Cradle\Profiler\InspectorTrait;
use Cradle\Profiler\LoggerTrait;

use Cradle\Resolver\StateTrait;

use Cradle\Http\HttpTrait;
use Cradle\Event\PipeTrait;

/**
 * Handler for micro framework calls. Combines both
 * Http handling and Event handling using event pipes.
 * 
 * @vendor   Cradle
 * @package  Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class FrameHttp
{	
	use HttpTrait,
		InstanceTrait, 
		LoopTrait, 
		ConditionalTrait,
		InspectorTrait, 
		LoggerTrait, 
		StateTrait,
		PipeTrait,
		PackageTrait 
		{ 
			StateTrait::__callResolver as __call;
			HttpTrait::route as routeHttp;
			PackageTrait::register as registerPackage;
			PackageTrait::__constructPackage as __construct;
		}
	
	/**
	 * Registers and initializes a plugin
	 *
	 * @param *string $vendor The vendor/package name
	 *
	 * @return FrameHttp
	 */
	public function register($vendor)
	{
		//if it's callable
		if(is_callable($vendor)) {
			//it's not a package
			//it's a preprocess
			return $this->preprocess($vendor);
		}

		return $this->registerPackage($vendor);
	}

	/**
     * Adds routing middleware
     *
     * @param *string   $method   The request method
     * @param *string   $path     The route path
     * @param *callable $callback The middleware handler
     *
     * @return FrameHttp
     */
    public function route($method, $path, $callback)
    {
		//if it's a string
		if(is_string($callback)) {
			//we are going to make a flow
			$flow = func_get_args();
			$method = array_shift($flow);
			$path = array_shift($flow);
			$event = $method . ' ' . $path;
			
			//which is now an event driven route
			$this->flow($event, ...$flow);

			$callback = function($request, $response) use ($event) {
				$this->trigger($event, $request, $response);
			};
		}
		
		$this->routeHttp($method, $path, $this->bindCallback($callback));
		
		return $this;
    }
}