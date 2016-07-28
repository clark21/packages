<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Frame;

use Cradle\Http\HttpHandler;

/**
 *
 * @vendor   Cradle
 * @package  Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class FrameHttp extends HttpHandler
{	
	use FrameTrait { FrameTrait::register as registerPackage; }
	
	/**
	 * Registers and initializes a plugin
	 *
	 * @param *string $vendor The vendor/package name
	 *
	 * @return FrameService
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
     * @param string   $method   The request method
     * @param string   $path     The route path
     * @param function $callback The middleware handler
     *
     * @return FrameService
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
		
		parent::route($method, $path, $this->bindCallback($callback));
		
		return $this;
    }
}