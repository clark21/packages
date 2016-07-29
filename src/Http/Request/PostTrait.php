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
 * Designed for the Request Object; Adds methods to store $_POST data
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait PostTrait
{
	/**
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name    The key name in the POST
	 * @param mixed       ...$args
	 *
	 * @return mixed
	 */
	public function getPost($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('post');
		}
		
		return $this->get('post', $name, ...$args);
	}
	
	/**
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name    The key name in the POST
	 * @param mixed       ...$args
	 *
	 * @return bool
	 */
	public function hasPost($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->exists('post');
		}
		
		return $this->exists('post', $name, ...$args);
	}
	
	/**
	 * Sets POST
	 *
	 * @param *mixed $data
	 * @param mixed  ...$args
	 *
	 * @return PostTrait
	 */
	public function setPost($data, ...$args)
	{
		if(is_array($data)) {
			return $this->set('post', $data);
		}
		
		if(count($args) === 0) {
			return $this;
		}
		
		return $this->set('post', $data, ...$args);
	}
}
