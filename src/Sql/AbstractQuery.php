<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql;

/**
 * Generates select query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
abstract class AbstractQuery
{
    /**
     * @var string $table most queries deal with tables
     */
    protected $table = null;

    /**
     * Transform class to string using
     * getQuery
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }
    
    /**
     * Returns the string version of the query
     *
     * @param bool
     *
     * @return string
     */
    abstract public function getQuery();
}
