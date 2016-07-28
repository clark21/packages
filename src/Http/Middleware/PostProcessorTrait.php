<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Middleware;

use Cradle\Http\Middleware;

/**
 *
 * @package  Cradle
 * @category Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait PostProcessorTrait
{
	/**
     * @var Middleware|null $preProcessor
     */
    protected $postProcessor = null;
    
    /**
     * Returns a middleware object
     *
     * @return MiddlewareInterface
     */
    public function getPostprocessor()
    {
        if(is_null($this->postProcessor)) {
			if(method_exists($this, 'resolve')) {
				$this->setPostprocessor($this->resolve(Middleware::class));
			} else {
				$this->setPostprocessor(new Middleware());
			}
		}

        return $this->postProcessor;
    }
    
    /**
     * Adds middleware
     *
     * @param function $callback The middleware handler
     *
     * @return PreProcessorTrait
     */
    public function postprocess($callback)
    {
        $this->getPostprocessor()->register($callback);
        return $this;
    }
    
    /**
     * Sets the middleware to use
     *
     * @param MiddlewareInterface $middleare
     *
     * @return PreProcessorTrait
     */
    public function setPostprocessor(MiddlewareInterface $middleware)
    {
        $this->postProcessor = $middleware;
        
        return $this;
    }
}
