<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Router;

/**
 * Handles method-path matching and routing
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
interface RouterInterface
{      
    /**
	 * Returns all the routes that match this method and path
	 *
	 * @param *string $method GET, POST, PUT, DELETE, etc..
	 * @param *string $path   The request path
	 *
	 * @return array
	 */
	public function match($method, $path);
	
	/**
     * Process routes
     *
     * @return bool
     */
    public function process(RequestInterface $request, ...$args);
    
    /**
     * Adds routing middleware
     *
     * @param string   $method   The request method
     * @param string   $path     The route path
     * @param function $callback The middleware handler
     *
     * @return RouterInterface
     */
    public function route($method, $path, $callback);
}
