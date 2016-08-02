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
use Cradle\Resolver\ResolverException;

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
            HttpTrait::route as routeHttp;
            PackageTrait::register as registerPackage;
            PackageTrait::__constructPackage as __construct;
        }
    
    /**
     * Attempts to use __callData then __callResolver
     *
     * @param *string $name name of method
     * @param *array  $args arguments to pass
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        try {
            return $this->__callResolver($name, $args);
        } catch (ResolverException $e) {
            throw new FrameException($e->getMessage());
        }
    }
    
    /**
     * Exports a flow to another external interface
     *
     * @param *string $event
     * @param bool    $map
     *
     * @return Closure|array
     */
    public function export($event, $map = false)
    {
        $handler = $this;
        
        $next = function (...$args) use ($handler, $event, $map) {
            $request = $handler->getRequest();
            $response = $handler->getResponse();

            $meta = $handler
                //do this directly from the handler
                ->getEventHandler()
                //trigger
                ->trigger($event, $request, $response, ...$args)
                //if our events returns false
                //lets tell the interface the same
                ->getMeta();
            
            //no map ? let's try our best
            //if we have meta
            if ($meta) {
                //return the response
                return $response->getContent(true);
            }
            
            //otherwise return false
            return false;
        };
        
            if (!$map) {
                return $next;
            }
        
            $request = $handler->getRequest();
            $response = $handler->getResponse();
        
            return array($request, $response, $next);
    }
    
    /**
     * Imports a set of flows
     *
     * @param *array $flows
     *
     * @return FrameHttp
     */
    public function import(array $flows)
    {
        foreach ($flows as $flow) {
            //it's gotta be an array
            if (!is_array($flow)) {
                continue;
            }
            
            $this->flow(...$flow);
        }
        
        return $this;
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
        if (is_callable($vendor)) {
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
        if (is_string($callback)) {
            //we are going to make a flow
            $flow = func_get_args();
            $method = array_shift($flow);
            $path = array_shift($flow);
            $event = $method . ' ' . $path;
            
            //which is now an event driven route
            $this->flow($event, ...$flow);

            $callback = function ($request, $response) use ($event) {
                $this->trigger($event, $request, $response);
            };
        }
        
        $this->routeHttp($method, $path, $this->bindCallback($callback));
        
        return $this;
    }
}
