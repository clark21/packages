<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http;

use Cradle\Http\Request\RequestInterface;
use Cradle\Http\Router\RouterInterface;

/**
 * Handles method-path matching and routing
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Router implements RouterInterface
{      
    /**
     * @var array $routes A list of route callbacks
     */
    protected $routes = [];
	
	/**
	 * Returns all the routes that match this method and path
	 *
	 * @param *string $method GET, POST, PUT, DELETE, etc..
	 * @param *string $path   The request path
	 *
	 * @return array
	 */
	public function match($method, $path)
	{
		$method = strtoupper($method);
		$matches = [];

		//if no routing on this
        if (!isset($this->routes[$method])
        	|| !is_array($this->routes[$method])
		) {
            return $matches;
        }
        
        //determine the route
        foreach ($this->routes[$method] as $route) {
            $regex = str_replace('**', '!!', $route['pattern']);
            $regex = str_replace('*', '([^/]*)', $regex);
            $regex = str_replace('!!', '(.*)', $regex);
            
            $regex = '#^'.$regex.'(.*)#';
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            
			$route['method'] = $method;
			$route['path'] = $path;
			$route['variables'] = $this->getVariables($matches);
			
			$matches[] = $route;
        }
		
		return $matches;
	}

    /**
     * Process routes
     *
     * @return bool
     */
    public function process(RequestInterface $request, ...$args)
    {
		$path = $request->getPath('string');
		$method = $request->getMethod();
		$routes = $this->match($method, $path);
		
		foreach($routes as $route) {
			$callback = $route['callback'];
			
			//the request object should 
			//be clean of objects
			unset($route['callback']);
			
			$request->setRoute($route);
			
			$results = call_user_func($callback, $request, ...$args);
			
			if ($results === false) {
				return false;
			}
		}
        
        return true;
    }
    
    /**
     * Adds routing middleware
     *
     * @param string   $method   The request method
     * @param string   $path     The route path
     * @param function $callback The middleware handler
     *
     * @return Router
     */
    public function route($method, $path, $callback)
    {
        $method = strtoupper($method);
        
        if ($method === 'ALL') {
            return $this
                ->route('get', $path, $callback)
                ->route('post', $path, $callback)
                ->route('put', $path, $callback)
                ->route('delete', $path, $callback);
        }
		
        $this->routes[$method][] = [
			'pattern' => $path, 
			'callback' => $callback
		];
		
        return $this;
    }
    
    /**
     * Returns a dynamic list of variables
     * based on the given pattern and path
     *
     * @param array $matches Matches usually from a preg method
     *
     * @return array
     */
    protected function getVariables($matches)
    {
        $variables = [];
        
        if (!is_array($matches)) {
            return $variables;
        }
        
        array_shift($matches);
        
        foreach ($matches as $path) {
            $variables = array_merge($variables, explode('/', $path));
        }
        
        foreach ($variables as $i => $variable) {
            if (!$variable) {
                unset($variables[$i]);
            }
        }
        
        return array_values($variables);
    }
}
