<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http;

use Cradle\Event\EventTrait;
use Cradle\Event\EventHandler;
use Cradle\Resolver\ResolverTrait;
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
    use EventTrait, ResolverTrait;
    
    /**
     * Allow to pass a custom EventHandler
     */
    public function __construct(EventHandler $handler = null)
    {
        //but we do need one
        if (is_null($handler)) {
            $handler = $this->resolve(EventHandler::class);
        }

        $this->setEventHandler($handler);
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
        $event = $method.' '.$path;

        return $this
            ->getEventHandler()
            ->trigger($event, $request, ...$args)
            ->getMeta();
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
        if ($method === 'ALL') {
            return $this
                ->route('get', $path, $callback)
                ->route('post', $path, $callback)
                ->route('put', $path, $callback)
                ->route('delete', $path, $callback);
        }

        $separator = md5(uniqid());

        $regex = str_replace('**', $separator, $path);
        $regex = str_replace('*', '([^/]+)', $regex);
        $regex = str_replace($separator, '(.*)', $regex);

        $event = '#^' . $method . '\s' . $regex . '$#is';
        
        $handler = $this->getEventHandler();
        
        $handler->on($event, function (
            RequestInterface $request,
            ...$args
        ) use (
            $handler,
            $callback,
            $method,
            $path
        ) {
            $route = $handler->getMeta();
            $variables = array();
            
            //sanitize the variables
            foreach ($route['variables'] as $variable) {
                if (strpos($variable, '/') === false) {
                    $variables[] = $variable;
                    continue;
                }
                
                $variables = array_merge($variables, explode('/', $variable));
            }

            $request->setRoute(array(
                'method' => $method,
                'path' => $path,
                'variables' => $variables
            ));
            
            return call_user_func($callback, $request, ...$args);
        });

        return $this;
    }
}
