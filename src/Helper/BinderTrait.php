<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Helper;

/**
 *
 * @package  Cradle
 * @category Helper
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait BinderTrait
{
	/**
     * Binds callback with this scope
     *
     * @param *Closure $conditional should evaluate to true
     *
     * @return Condition
     */
    public function bindCallback(Closure $callback)
	{
		return $callback->bindTo($this, get_class($this));
	}
}
