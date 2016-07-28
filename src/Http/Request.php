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
 * Request Class
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Request extends Registry implements RequestInterface
{
	/**
	 * Returns CLI args if any 
	 *
	 * @return array|null
	 */
	public function getArgs()
	{
		return $this->get('args');
	}
	
	/**
	 * Returns final input stream
	 *
	 * @return string|null
	 */
	public function getContent()
	{
		return $this->get('body');
	}
	
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       $args
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
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       $args
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
	 * Returns method if set
	 *
	 * @return string|null
	 */
	public function getMethod()
	{
		return strtoupper($this->get('method'));
	}
	
	/**
	 * Returns path data given name or all path data
	 *
	 * @param string|null $name The key name in the path (string|array)
	 *
	 * @return string|array
	 */
	public function getPath($name = null)
	{
		if(is_null($name)) {
			return $this->get('path');
		}
		
		return $this->get('path', $name);
	}
	
	/**
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name The key name in the POST
	 * @param mixed       $args
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
	 * Returns string query if set
	 *
	 * @return string|null
	 */
	public function getQuery()
	{
		return $this->get('query');
	}
	
	/**
	 * Returns route data given name or all route data
	 *
	 * @param string|null $name The key name in the route
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getRoute($name = null, ...$args)
	{
		if(is_null($name)) {
			return $this->get('route');
		}
		
		return $this->get('route', $name, ...$args);
	}
	
	/**
	 * Returns SERVER data given name or all SERVER data
	 *
	 * @param string|null $name The key name in the SERVER
	 *
	 * @return mixed
	 */
	public function getServer($name = null)
	{
		if(is_null($name)) {
			return $this->get('server');
		}
		
		return $this->get('server', $name);
	}
	
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
	 * Returns route data given name or all route data
	 *
	 * @param string|null $index The variable index
	 *
	 * @return mixed
	 */
	public function getVariables($index = null)
	{
		if(is_null($index)) {
			return $this->getRoute('variables');
		}
		
		return $this->getRoute('variables', $index);
	}
	
	/**
	 * Returns true if has content
	 *
	 * @return bool
	 */
	public function hasContent()
	{
		return !$this->isEmpty('body');
	}
	
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       $args
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
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       $args
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
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name The key name in the POST
	 * @param mixed       $args
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
	 * Returns SERVER data given name or all SERVER data
	 *
	 * @param string|null $name The key name in the SERVER
	 *
	 * @return bool
	 */
	public function hasServer($name = null)
	{
		if(is_null($name)) {
			return $this->exists('server');
		}
		
		return $this->exists('server', $name);
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
	 * Returns true if method is the one given
	 *
	 * @param *string $method
	 *
	 * @return bool
	 */
	public function isMethod($method)
	{
		return strtoupper($method) === strtoupper($this->get('method'));
	}

	/**
	 * Loads default data given by PHP
	 *
	 * @return Request
	 */
	public function load()
	{
		global $argv;

		$this
			->setArgs($argv)
			->setContent(file_get_contents('php://input'));
		
		if(isset($_COOKIE)) {
			$this->setCookies($_COOKIE);
		}

		if(isset($_FILES)) {
			$this->setFiles($_FILES);
		}

		if(isset($_POST)) {
			$this->setPost($_POST);
		}

		if(isset($_SERVER)) {
			$this->setServer($_SERVER);
		}

		if(isset($_SESSION)) {
			//so whatever changes will be reflected
			$this->setSession($_SESSION);
		}
		
		return $this;
	}

	/**
	 * Sets CLI args
	 *
	 * @param *array|null
	 *
	 * @return Request
	 */
	public function setArgs($argv = null)
	{
		return $this->set('args', $argv);
	}

	/**
	 * Sets content
	 *
	 * @param *mixed $content
	 *
	 * @return Request
	 */
	public function setContent($content)
	{
		$this->set('body', $content);
		return $this;
	}

	/**
	 * Sets COOKIE
	 *
	 * @param *array $cookies
	 *
	 * @return Request
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
	
	/**
	 * Sets GET
	 *
	 * @param *array $get
	 *
	 * @return Request
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
	
	/**
	 * Sets request method
	 *
	 * @param *string $method
	 *
	 * @return Request
	 */
	public function setMethod($method)
	{
		return $this->set('method', $method);
	}
	
	/**
	 * Sets path given in string or array form
	 *
	 * @param *string|array $path
	 *
	 * @return Request
	 */
	public function setPath($path)
	{
		if(is_string($path)) {
			$array = explode('/', $path);
		} else if(is_array($path)) {
			$array = $path;
			$path = implode('/', $path);
		}
		
		return $this
			->setDot('path.string', $path)
			->setDot('path.array', $array);
	}
	
	/**
	 * Sets POST
	 *
	 * @param *array $post
	 *
	 * @return Request
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
	
	/**
	 * Sets query string
	 *
	 * @param *string $get
	 *
	 * @return Request
	 */
	public function setQuery($query)
	{
		return $this->set('query', $query);
	}
	
	/**
	 * Sets a request route
	 *
	 * @param *mixed $results
	 *
	 * @return Request
	 */
	public function setRoute(array $route)
	{	
		return $this->set('route', $route);
	}
	
	/**
	 * Sets SERVER
	 *
	 * @param *array $server
	 *
	 * @return Request
	 */
	public function setServer(array $server)
	{
		$this->set('server', $server);
		
		//if there is no path set
		if(!$this->exists('path') && isset($server['REQUEST_URI'])) {
			$path = $_SERVER['REQUEST_URI'];
		
			//remove ? url queries
			if (strpos($path, '?') !== false) {
				list($path, $tmp) = explode('?', $path, 2);
			}
			
			$this->setPath($path);
		}

		if(!$this->exists('method') && isset($server['REQUEST_METHOD'])) {
			$this->setMethod($_SERVER['REQUEST_METHOD']);
		}

		if(!$this->exists('query') && isset($server['QUERY_STRING'])) {
			$this->setQuery($_SERVER['QUERY_STRING']);
		}
		
		return $this;
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
