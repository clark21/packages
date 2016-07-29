<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Request;

/**
 * Designed for the Request Object; Adds methods to store $_COOKIE data
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait CookieTrait
{
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       ...$args
	 *
	 * @return mixed
	 */
	public function getCookies($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('cookie');
		}
		
		return $this->get('cookie', $name, ...$args);
	}
	
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       ...$args
	 *
	 * @return bool
	 */
	public function hasCookies($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->exists('cookie');
		}
		
		return $this->exists('cookie', $name, ...$args);
	}

	/**
	 * Sets COOKIE
	 *
	 * @param *array $data
	 * @param mixed  ...$args
	 *
	 * @return CookieTrait
	 */
	public function setCookies($data, ...$args)
	{
		if(is_array($data)) {
			return $this->set('cookie', $data);
		}
		
		if(count($args) === 0) {
			return $this;
		}
		
		return $this->set('cookie', $data, ...$args);
	}
}
