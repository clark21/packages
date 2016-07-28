<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Dispatcher;

use Cradle\Http\Response\ResponseInterface;

/**
 * Express style server class implementation
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
interface DispatcherInterface
{
    /**
     * Evaluates the response
     * in order to determine the
     * output. Then of course,
     * output it
     *
     * @param Response $response The response object to evaluate
     *
     * @return DispatcherInterface
     */
    public function output(ResponseInterface $response);
	
    /**
     * Starts to process the request
     *
     * @return array with request and response inside
     */
    public function dispatch(ResponseInterface $response);
    
    /**
     * Returns if we were able to output
     * something
     *
     * @return bool
     */
    public function isSuccessful();
}
