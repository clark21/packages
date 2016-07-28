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
trait FileTrait
{
	/**
	 * Returns file data given name or all files
	 *
	 * @param string|null $name The key name in the FILES
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getFiles($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('files');
		}
		
		return $this->get('files', $name, ...$args);
	}
	
	/**
	 * Returns file data given name or all files
	 *
	 * @param string|null $name The key name in the FILES
	 * @param mixed       $args
	 *
	 * @return bool
	 */
	public function hasFiles($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->exists('files');
		}
		
		return $this->exists('files', $name, ...$args);
	}
	
	/**
	 * Sets FILES
	 *
	 * @param *array $files
	 *
	 * @return Request
	 */
	public function setFiles($data, ...$args)
	{
		if(is_array($data)) {
			return $this->set('files', $data);
		}
		
		if(count($args) === 0) {
			return $this;
		}
		
		return $this->set('files', $data, ...$args);
	}
}
