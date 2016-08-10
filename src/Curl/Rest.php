<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Curl;

use Closure;
use Cradle\Event\EventTrait;

use Cradle\Helper\InstanceTrait;
use Cradle\Helper\LoopTrait;
use Cradle\Helper\ConditionalTrait;

use Cradle\Profiler\InspectorTrait;
use Cradle\Profiler\LoggerTrait;

use Cradle\Resolver\StateTrait;
use Cradle\Resolver\ResolverException;

/**
 * A handy tool for building REST Interfaces
 *
 * @vendor   Cradle
 * @package  Curl
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Rest
{
    use EventTrait,
        InstanceTrait,
        LoopTrait,
        ConditionalTrait,
        InspectorTrait,
        LoggerTrait,
        StateTrait;

    /**
     * @const string ENCODE_JSON Encode option
     */
    const ENCODE_JSON = 'json';
    
    /**
     * @const string ENCODE_QUERY Encode option
     */
    const ENCODE_QUERY = 'query';
    
    /**
     * @const string ENCODE_XML Encode option
     */
    const ENCODE_XML = 'xml';
    
    /**
     * @const string ENCODE_RAW Encode option
     */
    const ENCODE_RAW = 'raw';
    
    /**
     * @const string METHOD_GET Request method option
     */
    const METHOD_GET = 'GET';
    
    /**
     * @const string METHOD_POST Request method option
     */
    const METHOD_POST = 'POST';
    
    /**
     * @const string METHOD_PUT Request method option
     */
    const METHOD_PUT = 'PUT';
    
    /**
     * @const string METHOD_DELETE Request method option
     */
    const METHOD_DELETE = 'DELETE';
    
    /**
     * @var string|null $host The root host name
     */
    protected $host = null;
    
    /**
     * @var array $data The request body
     */
    protected $data = [];
    
    /**
     * @var string|null $agent The header user agent
     */
    protected $agent = null;
    
    /**
     * @var array $paths
     */
    protected $paths = [];
    
    /**
     * @var array $headers The list of headers
     */
    protected $headers = [];
    
    /**
     * @var bool $map to override the curl call
     */
    protected $map = null;
    
    /**
     * @var array $routes Used by children to narrow down the possible options
     */
    protected $routes = [];
    
    /**
     * Processes set, post, put, delete, get, etc.
     *
     * @param *string $name The name of the method
     * @param *array  $args The arguments trying to be passed
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        //if it starts with set
        if (strpos($name, 'set') === 0) {
            //determine the separator
            $separator = '_';
            
            if (isset($args[1]) && is_scalar($args[1])) {
                $separator = (string) $args[1];
            }
            
            //get the key
            $key = $this->getKey('set', $name, $separator);
            
            //just set it, we will validate on send
            $this->data[$key] = $args[0];
        
            return $this;
        }
        
        //if it starts with set
        if (strpos($name, 'add') === 0) {
            //determine the separator
            $separator = '_';
            
            if (isset($args[1]) && is_scalar($args[1])) {
                $separator = (string) $args[1];
            }
            
            //get the key
            $key = $this->getKey('add', $name, $separator);
            
            //just set it, we will validate on send
            $this->data[$key][] = $args[0];
        
            return $this;
        }
        
        //get the path equivilent
        $path = $this->getKey('', $name, '/');
        
        if (isset($this->routes[$path])) {
            $meta = $this->routes[$path];
            if (is_string($meta)) {
                $meta = $this->routes[$meta];
            }
            
            $path = $this->getRoute($meta['route'], $args);
            
            return $this->send($meta['method'], $path, $meta);
        }
        
        //default actions test
        switch (true) {
            case strpos($name, 'get') === 0:
                $path = $this->getPath('get', $name, $args);
                return $this->send(self::METHOD_GET, $path);
            case strpos($name, 'create') === 0:
                $path = $this->getPath('create', $name, $args);
                return $this->send(self::METHOD_POST, $path);
            case strpos($name, 'post') === 0:
                $path = $this->getPath('post', $name, $args);
                return $this->send(self::METHOD_POST, $path);
            case strpos($name, 'update') === 0:
                $path = $this->getPath('update', $name, $args);
                return $this->send(self::METHOD_POST, $path);
            case strpos($name, 'put') === 0:
                $path = $this->getPath('put', $name, $args);
                return $this->send(self::METHOD_PUT, $path);
            case strpos($name, 'remove') === 0:
                $path = $this->getPath('remove', $name, $args);
                return $this->send(self::METHOD_DELETE, $path);
            case strpos($name, 'delete') === 0:
                $path = $this->getPath('delete', $name, $args);
                return $this->send(self::METHOD_DELETE, $path);
        }
        
        //if it's a factory method match
        if (count($args) === 0) {
            //add this to the path
            $this->paths[] = $this->getKey('', $name, '/');
            return $this;
        }
        
        try {
            return $this->__callResolver($name, $args);
        } catch (ResolverException $e) {
            throw new RestException($e->getMessage());
        }
    }
    
    /**
     * Sets up the host and if we are in tet mode
     *
     * @param *string      $host The root host
     * @param Closure|null $map  Whether to just get the meta
     *
     * @return Rest
     */
    public function __construct($host, Closure $map = null)
    {
        $this->host = $host;
        $this->map = $map;
    }
    
    /**
     * Add headers into this request
     *
     * @param *string|array $key   The header name
     * @param scalar|null   $value The header value
     *
     * @return Rest
     */
    public function addHeader($key, $value = null)
    {
        //if it's an array
        if (is_array($key)) {
            //warning this overwrites existing headers
            $this->headers = $key;
            return $this;
        }
        
        //if the value is null
        if (is_null($value)) {
            $this->headers[] = $key;
            return $this;
        }
        
        //else it should be key value
        $this->headers[] = $key . ': ' . $value;
        return $this;
    }
    
    /**
     * Add custom routing
     * this is normally for classes
     * wishing to extend this class
     * and testing
     *
     * @param *string        $path The route path
     * @param *string|array  $meta The route meta or forwarding meta
     *
     * @return Rest
     */
    public function addRoute($path, $meta)
    {
        $this->routes[$path] = $meta;
        
        return $this;
    }
    
    /**
     * Set the raw data
     *
     * @param *array|scalar $data  Either the entire data or a key
     * @param mixed         $value The value of the key
     *
     * @return Rest
     */
    public function setData($data, $value = null)
    {
        if (is_array($data)) {
            $this->data = $data;
            
            return $this;
        }
        
        $this->data[$data] = $value;
        
        return $this;
    }
    
    /**
     * Sends off this request to cURL
     *
     * @param *string $method The request method
     * @param *string $path   The end point path
     * @param array   $meta   Route meta
     *
     * @return mixed
     */
    public function send($method, $path, array $meta = [])
    {
        //get the meta data for this url call
        $meta = $this->getMetaData($method, $path, $meta);
        
        //extract the meta data
        $url = $meta['url'];
        $data = $meta['post'];
        $agent = $meta['agent'];
        $encode = $meta['encode'];
        $headers = $meta['headers'];
        
        // send it into curl
        $request = CurlHandler::i($this->map)
            ->setUrl($url)
            ->setConnectTimeout(10) // sets connection timeout to 10 sec.
            ->setFollowLocation(true) // sets the follow location to true
            ->setTimeout(60) // set page timeout to 60 sec
            ->verifyPeer(false) // verifying Peer must be boolean
            //if the agent is set
            ->when($agent, function () use ($agent) {
                $this->setUserAgent($agent); // set USER_AGENT
            })
            //if there are headers
            ->when(!empty($headers), function () use ($headers) {
                $this->setHeaders($headers); // set headers
            })
            //set the custom request
            ->when($method == 'PUT' || $method == 'DELETE', function () use ($method) {
                $this->setCustomRequest($method);
            })
            //when post or put
            ->when($method == 'POST' || $method == 'PUT', function () use ($data) {
                if (empty($data)) {
                    return;
                }
                
                //set the post data
                $this->setPostFields($data);
            });
       
        //how should we return the data ?
        switch ($encode) {
            case self::ENCODE_QUERY:
                $response = $request->getQueryResponse(); // get the query response
                break;
            case self::ENCODE_JSON:
                $response = $request->getJsonResponse(); // get the json response
                break;
            case self::ENCODE_XML:
                $response = $request->getSimpleXmlResponse(); // get the xml response
                break;
            case self::ENCODE_RAW:
            default:
                $response = $request->getResponse(); // get the raw response
                break;
        }
        
        return $response;
    }
    
    /**
     * Returns any problems with the request
     *
     * @param *string $method The request method
     * @param *string $path   The request path
     * @param *string $meta   The route meta
     * @param *array  $data   The request body
     */
    private function throwErrors($method, $path, $meta, array $data)
    {
        //validate data
        if (!isset($meta['data'])
            || empty($meta['data'])
        ) {
            return;
        }
        
        // NOTE: APIs update more frequent
        // than we can update these libraries
        // In this case we should allow for
        // random variables
        // -----------------------------------
        //if they made up a key value
        //foreach($data as $key => $value) {
        //    if (!isset($meta['data'][$key])) {
        //        //disallow random key/values
        //        return sprintf(self::FAIL_DATA, $key);
        //    }
        //}
        
        foreach ($meta['data'] as $key => $valid) {
            //normalize validations
            if (!is_array($valid)) {
                $valid = [$valid];
            }
            
            //loop through validations
            foreach ($valid as $validation) {
                //same here
                if (is_string($validation)) {
                    $validation = [$validation];
                }
                
                //if it's required
                //and it's not set
                if ($validation[0] === 'required'
                && !isset($data[$key])) {
                    throw RestException::forMissingRequired($key);
                }
                
                //else we should check if the field is valid
                if (isset($data[$key]) && !$this->isFieldValid($data[$key], $validation)) {
                    throw RestException::forInvalidData($key);
                }
            }
        }
    }
    
    /**
     * Used by magic methods, this is used to
     * parse out the method name and return
     * the translated meaning
     *
     * @param *string $action    The prefix action
     * @param *string $method    The magic method
     * @param string  $separator How to delimit the key name
     *
     * @return string
     */
    private function getKey($action, $method, $separator = '_')
    {
        //setSomeSample -> post/Some/Sample
        $key = preg_replace("/([A-Z0-9])/", $separator."$1", $method);
        //set/Some/Sample -> /some/sample
        return trim(strtolower(substr($key, strlen($action))), $separator);
    }
    
    /**
     * Used mainly for testing or passing out the call
     * for further processing
     *
     * @param *string $method The request method
     * @param *string $path   The request path
     * @param *string $meta   The route meta
     *
     * @return array
     */
    private function getMetaData($method, $path, array $meta)
    {
        //if no host
        if (!$this->host) {
            throw RestException::forMissingHost();
        }
        
        //variable list
        $host = $this->host;
        $data = $this->data;
        $agent = $this->agent;
        $method = strtoupper($method);
        $headers = $this->headers;
        $requestEncode = self::ENCODE_QUERY;
        $responseEncode = self::ENCODE_JSON;
        
        //if we have custom routes
        if (!empty($meta)) {
            //check for errors
            $this->throwErrors(
                $method,
                $path,
                $meta,
                $data
            );
            
            //get the request and response encodes
            $requestEncode = $this->getRequestEncode($meta);
            $responseEncode = $this->getResponseEncode($meta);
            
            //get the url query and post
            list($query, $data) = $this->getQueryAndPost($meta, $data);
        }
        
        //if the method is a put or post
        if ($method === 'POST' || $method === 'PUT') {
            //figure out how to encode it
            switch ($requestEncode) {
                case self::ENCODE_JSON:
                    $data = json_encode($data);
                    break;
                case self::ENCODE_QUERY:
                default:
                    $data = http_build_query($data);
                    break;
            }
        //it's a get or delete
        } else {
            //let the query be the data
            $query = $data;
        }
        
        //form the url
        $url = $host . $path;
                    
        //if we have a query
        if (!empty($query)) {
            //add it on to the url
            $url .= '?' . http_build_query($query);
        }
        
        //we are done
        return [
            'url' => $url,
            'post' => $data,
            'agent' => $agent,
            'method' => $method,
            'encode' => $responseEncode,
            'headers' => $headers
        ];
    }
    
    /**
     * Returns the compiled path
     *
     * @param *string $action The magic action prefix
     * @param *string $method The magic method name
     * @param *array  $args   The arguments for the method
     *
     * @return string
     */
    private function getPath($action, $method, $args)
    {
        //first get the key
        $key = $this->getKey($action, $method, '/');
        
        //add a trailing seperator
        $path = '/' . $key;
        
        //if there are paths
        if (!empty($this->paths)) {
            //prefix the paths to the path
            $path = '/' . implode('/', $this->paths) . $path;
        }
        
        //if there are no arguments
        if (!empty($args)) {
            //add that too
            $path .= '/' . implode('/', $args);
        }
        
        return str_replace('//', '/', $path);
    }
    
    /**
     * Figures out which data is the url query and the post
     *
     * @param *string $meta The route meta
     * @param *array  $data The actual request body
     *
     * @return array
     */
    private function getQueryAndPost($meta, $data)
    {
        $query = [];
        $fields = [];
        
        //if we have a query list
        if (isset($meta['query'])) {
            //use that as the fields
            $fields = $meta['query'];
        }
        
        //loop through the data sent
        foreach ($data as $key => $value) {
            //if it's a field
            if (in_array($key, $fields)) {
                //add that to the query
                $query[$key] = $value;
                //and unset the data
                unset($data[$key]);
            }
        }
        
        //return both the query and data
        return [$query, $data];
    }
    
    /**
     * Returns what kind of encoding the request should have
     *
     * @param *array $meta The route meta data
     *
     * @return string
     */
    private function getRequestEncode(array $meta)
    {
        $encode = self::ENCODE_QUERY;
        if (isset($meta['request'])) {
            $encode = $meta['request'];
        }
        
        return $encode;
    }
    
    /**
     * Returns what kind of encoding the response should have
     *
     * @param *array $meta The route meta data
     *
     * @return string
     */
    private function getResponseEncode(array $meta)
    {
        $encode = self::ENCODE_JSON;
        if (isset($meta['response'])) {
            $encode = $meta['response'];
        }
        
        return $encode;
    }
    
    /**
     * Returns the actual route based on the path pattern
     *
     * @param *string $path The request path
     * @param *array  $args The arguments from send
     *
     * @return string
     */
    private function getRoute($path, $args)
    {
        //replace *'s with each arg
        //if there are more args, add it to the end
        foreach ($args as $arg) {
            if (strpos($path, '*') !== false) {
                $path = preg_replace('/\*/', $arg, $path, 1);
                continue;
            }
            
            //We don't allow extra args
            //$path .= '/' . $arg;
        }
        
        return str_replace('//', '/', $path);
    }
    
    /**
     * Tests a field against many kinds of validations
     *
     * @param *string $value      The value to be tested
     * @param *array  $validation The test parameter
     *
     * @return bool
     */
    private function isFieldValid($value, $validation)
    {
        switch ($validation[0]) {
            case 'empty':
                return !empty($value);
            case 'one':
                return empty($value) || in_array($value, $validation[1]);
            case 'number':
                return empty($value) || is_numeric($value);
            case 'int':
                return empty($value) || $this('validation', $value)->isType('int', true);
            case 'float':
                return empty($value) || $this('validation', $value)->isType('float', true);
            case 'bool':
                return empty($value) || $this('validation', $value)->isType('bool', true);
            case 'small':
                return empty($value) || $this('validation', $value)->isType('small', true);
            case 'date':
                return empty($value) || $this('validation', $value)->isType('date');
            case 'time':
                return empty($value) || $this('validation', $value)->isType('time');
            case 'email':
                return empty($value) || $this('validation', $value)->isType('email');
            case 'json':
                return empty($value) || $this('validation', $value)->isType('json');
            case 'url':
                return empty($value) || $this('validation', $value)->isType('url');
            case 'html':
                return empty($value) || $this('validation', $value)->isType('html');
            case 'cc':
                return empty($value) || $this('validation', $value)->isType('cc');
            case 'hex':
                return empty($value) || $this('validation', $value)->isType('hex');
            case 'slug':
                return empty($value) || $this('validation', $value)->isType('slug');
            case 'alphanum':
                return empty($value) || $this('validation', $value)->isType('alphanum');
            case 'alphanum-':
                return empty($value) || $this('validation', $value)->isType('alphanum-');
            case 'alphanum_':
                return empty($value) || $this('validation', $value)->isType('alphanum_');
            case 'alphanum-_':
                return empty($value) || $this('validation', $value)->isType('alphanum-_');
            case 'regex':
                return empty($value) || preg_match($validation[1], $value);
            case 'gt':
                return empty($value) || $value > $validation[1];
            case 'gte':
                return empty($value) || $value >= $validation[1];
            case 'lt':
                return empty($value) || $value < $validation[1];
            case 'lte':
                return empty($value) || $value <= $validation[1];
            case 'sgt':
                return empty($value) || strlen($value) > $validation[1];
            case 'sgte':
                return empty($value) || strlen($value) > $validation[1];
            case 'slt':
                return empty($value) || strlen($value) > $validation[1];
            case 'slte':
                return empty($value) || strlen($value) > $validation[1];
        }
        
        return true;
    }
}
