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
 * Generates insert query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera cblanquera@openovate.com
 * @standard PSR-2
 */
class QueryInsert extends AbstractQuery
{
    /**
     * @var array $setKey List of keys
     */
    protected $setKey = [];

    /**
     * @var array $setVal List of values
     */
    protected $setVal = [];
    
    /**
     * Set the table, if any
     *
     * @param string|null $table Table name
     */
    public function __construct($table = null)
    {
        if (is_string($table)) {
            $this->setTable($table);
        }
    }
    
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        $multiValList = [];
        foreach ($this->setVal as $val) {
            $multiValList[] = '('.implode(', ', $val).')';
        }
        
        return 'INSERT INTO '
            . $this->table . ' ('.implode(', ', $this->setKey)
            . ') VALUES ' . implode(", \n", $multiValList).';';
    }
    
    /**
     * Set clause that assigns a given field name to a given value.
     * You can also use this to add multiple rows in one call
     *
     * @param *string      $key   The column name
     * @param *scalar|null $value The column value
     * @param int          $index For what row is this for?
     *
     * @return Insert
     */
    public function set($key, $value, $index = 0)
    {
        if (!in_array($key, $this->setKey)) {
            $this->setKey[] = $key;
        }
        
        if (is_null($value)) {
            $value = 'null';
        } else if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        
        $this->setVal[$index][] = $value;
        return $this;
    }
    
    /**
     * Set the table name in which you want to delete from
     *
     * @param string $table The table name
     *
     * @return Insert
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
