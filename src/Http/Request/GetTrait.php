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
 * Designed for the Request Object; Adds methods to store $_GET data
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait GetTrait
{
	/**
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       ...$args
	 *
	 * @return mixed
	 */
	public function getGet($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('get');
		}
		
		return $this->get('get', $name, ...$args);
	}
	
	/**
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       ...$args
	 *
	 * @return bool
	 */
	public function hasGet($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->exists('get');
		}
		
		return $this->exists('get', $name, ...$args);
	}
	
	/**
	 * Sets GET
	 *
	 * @param *array $data
	 * @param mixed  ...$args
	 *
	 * @return GetTrait
	 */
	public function setGet($data, ...$args)
	{
		if(is_array($data)) {
			return $this->set('get', $data);
		}
		
		if(count($args) === 0) {
			return $this;
		}
		
		return $this->set('get', $data, ...$args);
	}
}
