<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Frame;

use Exception;

/**
 * Event exceptions
 *
 * @package  Cradle
 * @category Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class EventException extends Exception
{
	/**
	 * @const string ERROR_PACKAGE_NOT_FOUND Error template
	 */
	const ERROR_PACKAGE_NOT_FOUND = 'Could not find package: %s';

	/**
	 * @const ERROR_METHOD_NOT_FOUND Error template
	 */
	const ERROR_METHOD_NOT_FOUND = 'No method named %s was found';
	
    /**
     * Create a new exception for invalid callback
     *
     * @param *string $vendor
	 *
     * @return FrameException
     */
	public static function forPackageNotFound($vendor)
	{
		return new static(sprintf(static::ERROR_PACKAGE_NOT_FOUND, $vendor));
	}
	
    /**
     * Create a new exception for invalid callback
     *
     * @param *string $vendor
	 *
     * @return FrameException
     */
	public static function forMethodNotFound($name)
	{
		return new static(sprintf(static::ERROR_METHOD_NOT_FOUND, $name));
	}
}
