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
 * Designed for the Request Object; Adds methods to store $_REQUEST data
 *
 * @vendor   Cradle
 * @package  Http
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait StageTrait
{
    /**
     * Returns $_REQUEST given name or all $_REQUEST
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function getStage(...$args)
    {
        return $this->get('stage', ...$args);
    }
    
    /**
     * Returns true if has $_REQUEST given name or if $_REQUEST is set
     *
     * @param mixed ...$args
     *
     * @return bool
     */
    public function hasStage(...$args)
    {
        return $this->exists('stage', ...$args);
    }
    
    /**
     * Removes $_REQUEST given name or all $_REQUEST
     *
     * @param mixed ...$args
     *
     * @return bool
     */
    public function removeStage(...$args)
    {
        return $this->remove('stage', ...$args);
    }
    
    /**
     * Clusters request data together
     *
     * @param array $data
     *
     * @return StageTrait
     */
    public function setStage(array $data, $overwrite = true)
    {
        //one dimenstions soft setter
        foreach ($data as $key => $value) {
            if (!$overwrite && $this->exists('stage', $key)) {
                continue;
            }
            
            $this->set('stage', $key, $value);
        }
        
        return $this;
    }
}
