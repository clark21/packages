<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Http;

use Cradle\Data\Registry;

/**
 * Response Class
 *
 * @vendor   Cradle
 * @package  Server
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Response extends Registry implements ResponseInterface
{
	/**
	 * Adds a header parameter
	 *
	 * @param *string     $name  Name of the header
	 * @param string|null $value Value of the header
	 *
	 * @return Response
	 */
	public function addHeader($name, $value = null) 
	{
		if(!is_null($value)) {
			return $this->set('headers', $name, $value);
		}

		return $this->set('headers', $name, null);
	}
	
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
	 * Returns the content body
	 *
	 * @param bool $toString whether to actually make this a string
	 *
	 * @return mixed
	 */
	public function getContent($toString = false)
	{
		$content = $this->get('body');

		if(!$toString) {
			return $content;
		}
		
		//if it's not scalar
        if (!is_scalar($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
		
		$content = (string) $content;
		
		 if (!$content) {
            $content = '';
        }
		
		return $content;
	}
	
	/**
	 * Returns either the header value given
	 * the name or the all headers
	 *
	 * @return mixed
	 */
	public function getHeaders($name = null)
	{
		if(is_null($name)) {
			return $this->get('headers');
		}

		return $this->get('headers', $name);
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
	 * Returns the status code
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->get('code');
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
	 * Returns true if content is set
	 *
	 * @return bool
	 */
	public function hasContent()
	{
		$body = $this->get('body');
		return !is_scalar($body) || strlen($body);
	}
	
	/**
	 * Returns true if content is scalar
	 *
	 * @return bool
	 */
	public function isContentFlat()
	{
		return !is_scalar($this->get('body'));
	}
	
	/**
	 * Loads default data
	 *
	 * @return Response
	 */
	public function load()
	{
		$this
			->setHeader('Content-Type', 'text/html; charset=utf-8')
            ->setStatus(200, '200 OK');
		
		return $this;
	}
	
	/**
	 * Sets the content
	 *
	 * @param *mixed $content Can it be an array or string please?
	 *
	 * @return Response
	 */
	public function setContent($content)
	{	
		if(!is_scalar($content)) {
			$content = (array) $content;
		}
		
		return $this->set('body', $content);
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
	 * Sets a status code
	 *
	 * @param *int    $code   Status code
	 * @param *string $status The string literal code for header
	 *
	 * @return Response
	 */
	public function setStatus($code, $status) 
	{
		return $this
			->set('code', $code)
			->setHeader('Status', $status);
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
