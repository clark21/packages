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
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait SessionTrait
{	
	/**
	 * Returns SESSION data given name or all SESSION data
	 *
	 * @param string|null $name The key name in the SESSION
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getSession($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('session');
		}
		
		return $this->get('session', $name, ...$args);
	}
	
	/**
	 * Returns SESSION data given name or all SESSION data
	 *
	 * @param mixed $args
	 *
	 * @return bool
	 */
	public function hasSession(...$args)
	{
		return $this->exists('session', ...$args);
	}
	
	/**
	 * Sets SESSION
	 *
	 * @param *array $session
	 *
	 * @return Request
	 */
	public function setSession(&$data, ...$args)
	{
		if(is_array($data)) {
			//pass reference
			return $this->set('session', $data);
		}
		
		if(count($args) === 0) {
			return $this;
		}
		
		return $this->set('session', $data, ...$args);
	}
}
