<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Curl;

/**
 * Rest exceptions
 *
 * @package  Cradle
 * @category Curl
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class RestException extends CurlException
{
    /**
     * @const string ERROR_HOST_NOT_DEFINED Error template
     */
    const ERROR_HOST_NOT_DEFINED = 'Host is not defined';
    
    /**
     * @const string ERROR_DATA_NOT_EXIST Error template
     */
    const ERROR_DATA_NOT_EXIST = '%s does not exist';
    
    /**
     * @const string ERROR_MISSING_REQUIRED Error template
     */
    const ERROR_MISSING_REQUIRED = '%s is required';
    
    /**
     * @const string ERROR_INVALID Error template
     */
    const ERROR_INVALID = '%s does not have a valid value';
    
    /**
     * Create a new exception for missing host
     *
     * @return RestException
     */
    public static function forMissingHost()
    {
        return new static(static::ERROR_HOST_NOT_DEFINED);
    }
    
    /**
     * Create a new exception for missing data
     *
     * @param *string $key
     *
     * @return RestException
     */
    public static function forMissingData($key)
    {
        return new static(sprintf(static::ERROR_DATA_NOT_EXIST, $key));
    }
    
    /**
     * Create a new exception for missing required data
     *
     * @param *string $key
     *
     * @return RestException
     */
    public static function forMissingRequired($key)
    {
        return new static(sprintf(static::ERROR_MISSING_REQUIRED, $key));
    }
    
    /**
     * Create a new exception for invalid data
     *
     * @param *string $key
     *
     * @return RestException
     */
    public static function forInvalidData($key)
    {
        return new static(sprintf(static::ERROR_INVALID, $key));
    }
}
