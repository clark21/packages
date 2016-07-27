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
 * Generates update query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryUpdate extends QueryDelete
{
    /**
     * @var array $set List of key/values
     */
    protected $set = [];
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        $set = [];
        foreach ($this->set as $key => $value) {
            $set[] = "{$key} = {$value}";
        }
        
        return 'UPDATE '. $this->table
        . ' SET ' . implode(', ', $set)
        . ' WHERE '. implode(' AND ', $this->where).';';
    }
    
    /**
     * Set clause that assigns a given field name to a given value.
     *
     * @param *string      $key   The column name
     * @param *scalar|null $value The column value
     *
     * @return Update
     */
    public function set($key, $value)
    {
        if (is_null($value)) {
            $value = 'null';
        } else if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        
        $this->set[$key] = $value;
        
        return $this;
    }
}
