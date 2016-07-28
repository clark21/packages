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
 * Request Class
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
interface RequestInterface
{
	/**
	 * Returns CLI args if any 
	 *
	 * @return array|null
	 */
	public function getArgs();
	
	/**
	 * Returns final input stream
	 *
	 * @return string|null
	 */
	public function getContent();
	
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getCookies($name = null, ...$args);
	
	/**
	 * Returns file data given name or all files
	 *
	 * @param string|null $name The key name in the FILES
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getFiles($name = null, ...$args);
	
	/**
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getGet($name = null, ...$args);
	
	/**
	 * Returns method if set
	 *
	 * @return string|null
	 */
	public function getMethod();
	
	/**
	 * Returns path data given name or all path data
	 *
	 * @param string|null $name The key name in the path (string|array)
	 *
	 * @return string|array
	 */
	public function getPath($name = null);
	
	/**
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name The key name in the POST
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getPost($name = null, ...$args);
	
	/**
	 * Returns string query if set
	 *
	 * @return string|null
	 */
	public function getQuery();
	
	/**
	 * Returns SERVER data given name or all SERVER data
	 *
	 * @param string|null $name The key name in the SERVER
	 *
	 * @return mixed
	 */
	public function getServer($name = null);
	
	/**
	 * Returns SESSION data given name or all SESSION data
	 *
	 * @param string|null $name The key name in the SESSION
	 * @param mixed       $args
	 *
	 * @return mixed
	 */
	public function getSession($name = null, ...$args);
	
	/**
	 * Returns true if has content
	 *
	 * @return bool
	 */
	public function hasContent();
	
	/**
	 * Returns cookies given name or all cookies
	 *
	 * @param string|null $name The key name in the COOKIE
	 * @param mixed       $args
	 *
	 * @return bool
	 */
	public function hasCookies($name = null, ...$args);
	
	/**
	 * Returns file data given name or all files
	 *
	 * @param string|null $name The key name in the FILES
	 * @param mixed       $args
	 *
	 * @return bool
	 */
	public function hasFiles($name = null, ...$args);
	
	/**
	 * Returns GET data given name or all GET
	 *
	 * @param string|null $name The key name in the GET
	 * @param mixed       $args
	 *
	 * @return bool
	 */
	public function hasGet($name = null, ...$args);
	
	/**
	 * Returns POST data given name or all POST data
	 *
	 * @param string|null $name The key name in the POST
	 * @param mixed       $args
	 *
	 * @return bool
	 */
	public function hasPost($name = null, ...$args);
	
	/**
	 * Returns SERVER data given name or all SERVER data
	 *
	 * @param string|null $name The key name in the SERVER
	 *
	 * @return bool
	 */
	public function hasServer($name = null);
	
	/**
	 * Returns SESSION data given name or all SESSION data
	 *
	 * @param mixed $args
	 *
	 * @return bool
	 */
	public function hasSession(...$args);
	
	/**
	 * Returns true if method is the one given
	 *
	 * @param *string $method
	 *
	 * @return bool
	 */
	public function isMethod($method);

	/**
	 * Sets CLI args
	 *
	 * @param *array|null
	 *
	 * @return RequestInterface
	 */
	public function setArgs($argv = null);

	/**
	 * Sets content
	 *
	 * @param *mixed $content
	 *
	 * @return RequestInterface
	 */
	public function setContent($content);

	/**
	 * Sets COOKIE
	 *
	 * @param *array $cookies
	 *
	 * @return RequestInterface
	 */
	public function setCookies($data, ...$args);
	
	/**
	 * Sets FILES
	 *
	 * @param *array $files
	 *
	 * @return RequestInterface
	 */
	public function setFiles($data, ...$args);
	
	/**
	 * Sets GET
	 *
	 * @param *array $get
	 *
	 * @return RequestInterface
	 */
	public function setGet($data, ...$args);
	
	/**
	 * Sets request method
	 *
	 * @param *string $method
	 *
	 * @return RequestInterface
	 */
	public function setMethod($method);
	
	/**
	 * Sets path given in string or array form
	 *
	 * @param *string|array $path
	 *
	 * @return RequestInterface
	 */
	public function setPath($path);
	
	/**
	 * Sets POST
	 *
	 * @param *array $post
	 *
	 * @return RequestInterface
	 */
	public function setPost($data, ...$args);
	
	/**
	 * Sets query string
	 *
	 * @param *string $get
	 *
	 * @return RequestInterface
	 */
	public function setQuery($query);
	
	/**
	 * Sets a request route
	 *
	 * @param *mixed $results
	 *
	 * @return Request
	 */
	public function setRoute(array $route);
	
	/**
	 * Sets SERVER
	 *
	 * @param *array $server
	 *
	 * @return RequestInterface
	 */
	public function setServer(array $server);
	
	/**
	 * Sets SESSION
	 *
	 * @param *array $session
	 *
	 * @return RequestInterface
	 */
	public function setSession(&$data, ...$args);
}
