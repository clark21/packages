<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Data;

use ArrayAccess;
use Iterator;
use Countable;

use Cradle\Event\EventTrait;

use Cradle\Helper\InstanceTrait;
use Cradle\Helper\LoopTrait;
use Cradle\Helper\ConditionalTrait;

use Cradle\Profiler\CallerTrait;
use Cradle\Profiler\InspectorTrait;
use Cradle\Profiler\LoggerTrait;

use Cradle\Resolver\StateTrait;
use Cradle\Resolver\ResolverException;

/**
 * Core Factory Class
 *
 * @package  Cradle
 * @category Date
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Registry implements ArrayAccess, Iterator, Countable, RegistryInterface
{
	use DataTrait, 
		EventTrait, 
		InstanceTrait, 
		LoopTrait, 
		ConditionalTrait, 
		CallerTrait, 
		InspectorTrait, 
		LoggerTrait, 
		StateTrait
		{
			DataTrait::__getData as __get;
			DataTrait::__setData as __set;
			DataTrait::__toStringData as __toString;
		}
	
	/**
	 * Attempts to use __callData then __callResolver
	 *
	 * @param *string $name name of method
	 * @param *array  $args arguments to pass
	 *
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		try {
			return $this->__callData($name, $args);	
		} catch(DataException $e) {
		}
		
		try {
			return $this->__callResolver($name, $args);
		} catch(ResolverException $e) {
			throw new RegistryException($e->getMessage());
		}
	}
	
	/**
     * Presets the collection
     *
     * @param *mixed $data The initial data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
	
	/**
	 * Returns the entire data
	 * 
	 * @return array
	 */
	public function get(...$args)
	{
		if(count($args) === 0) {
			return $this->data;
		}

		return $this->getDot(implode('.', $args));
	}
	
	/**
	 * Sets the entire data
	 *
	 * @param *array $data
	 * 
	 * @return Registry
	 */
	public function set(...$args)
	{
		switch(count($args)) {
			case 0:
				//there's nothing to set
				return $this;
			case 1:
				if(is_array($args[0])) {
					$this->data = $args[0];
				}
				return $this;
			default:
				$value = array_pop($args);
				return $this->setDot(implode('.', $args), $value);
		}
	}
}
