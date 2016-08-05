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
 * Designed for the Response Object; Adds methods to process REST type responses
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
     *
     * @param *string $field
     * @param *string $message
     *
     * @return RestTrait
     */
    public function addValidation($field, $message)
    {
        $args = func_get_args();

        return $this->set('json', 'validation', ...$args);
    }

    /**
     * Returns JSON results if still in array mode
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function getResults(...$args)
    {
        if (!count($args)) {
            return $this->getDot('json.results');
        }
        
        return $this->get('json', 'results', ...$args);
    }
    
    /**
     * Returns JSON validations if still in array mode
     *
     * @param string|null $name
     * @param mixed       ...$args
     *
     * @return mixed
     */
    public function getValidation($name = null, ...$args)
    {
        if (is_null($name)) {
            return $this->getDot('json.validation');
        }
        
        return $this->get('json', 'validation', $name, ...$args);
    }

    /**
     * Returns true if there's any JSON
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function hasJson(...$args)
    {
        if (!count($args)) {
            return $this->exists('json');
        }
        
        return $this->exists('json', ...$args);
    }
    
    /**
     * Sets a JSON error message
     *
     * @param *bool  $status  True if there is an error
     * @param string $message A message to describe this error
     *
     * @return RestTrait
     */
    public function setError($status, $message = null)
    {
        $this->setDot('json.error', $status);
        
        if (!is_null($message)) {
            $this->setDot('json.message', $message);
        }
        
        return $this;
    }
    
    /**
     * Sets a JSON result
     *
     * @param *mixed $data
     * @param mixed  ...$args
     *
     * @return RestTrait
     */
    public function setResults($data, ...$args)
    {
        if (is_array($data) || count($args) === 0) {
            return $this->setDot('json.results', $data);
        }
        
        return $this->set('json', 'results', $data, ...$args);
    }
}
