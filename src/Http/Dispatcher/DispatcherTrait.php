<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Dispatcher;

use Cradle\Http\Dispatcher;

/**
 *
 * @package  Cradle
 * @category Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait DispatcherTrait
{
	/**
     * @var HttpDispatcher|null $dispatcher Response object to use
     */
    protected $dispatcher = null;
    
    /**
     * Returns a response object
     *
     * @return HttpDispatcher
     */
    public function getDispatcher()
    {
        if(is_null($this->dispatcher)) {
			$this->setDispatcher($this->resolve(HttpDispatcher::class));
		}

        return $this->dispatcher;
    }
    
    /**
     * Sets the dispatcher to use
     *
     * @param HttpDispatcher $dispatcher
     *
     * @return HttpHandler
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        
        return $this;
    }
}
