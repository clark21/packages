<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http\Response;

/**
 * Response Class
 *
 * @vendor   Cradle
 * @package  Server
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait RestTrait
{
	/**
	 * Adds a JSON validation message
	 * warning: This turns the body into an array
	 *
	 * @param *string $field
	 * @param *string $message
	 */
	public function addValidation($field, $message)
	{
		$args = func_get_args();

		return $this->set('body', 'validation', ...$args);
	}

	/**
	 * Returns JSON results if still in array mode
	 *
	 * @return mixed
	 */
	public function getResults(...$args)
	{
		if(!count($args))
		{
			return $this->getDot('body.results');	
		}
		
		return $this->get('body', 'results', ...$args);
	}
	
	/**
	 * Returns JSON validations if still in array mode
	 *
	 * @param string|null $name
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getValidation($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->getDot('body.validation');
		}
		
		return $this->get('body', 'validation', $name, ...$args);
	}
	
	/**
	 * Sets a JSON error message
	 * warning: This turns the body into an array
	 *
	 * @param *mixed $content Can it be an array or string please?
	 *
	 * @return Response
	 */
	public function setError($status, $message = null)
	{
		$this->setDot('body.error', $status);
		
		if(!is_null($message)) {
			$this->setDot('body.message', $message);
		}
		
		return $this;
	}
	
	/**
	 * Sets a JSON result
	 * warning: This turns the body into an array
	 *
	 * @param *mixed $results
	 *
	 * @return Response
	 */
	public function setResults($data, ...$args)
	{	
		if(is_array($data) || count($args) === 0) {
			return $this->setDot('body.results', $data);
		}
		
		return $this->set('body', 'results', $data, ...$args);
	}
}
