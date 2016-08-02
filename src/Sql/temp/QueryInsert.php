<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql\PostGreSql;

use Cradle\Sql\QueryInsert as SqlQueryInsert;

/**
 * Generates insert query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryInsert extends SqlQueryInsert
{
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
        
        return 'INSERT INTO "'. $this->table
            . '" ("'.implode('", "', $this->setKey).'") VALUES '
            . implode(", \n", $multiValList).';';
    }
    
    /**
     * Set clause that assigns a given field name to a given value.
     * You can also use this to add multiple rows in one call
     *
     * @param *string      $key   The column name
     * @param *scalar|null $value The column value
     * @param int          $index For what row is this for?
     *
     * @return this
     * @notes loads a set into registry
     */
    public function set($key, $value, $index = 0)
    {
        if (!in_array($key, $this->setKey)) {
            $this->setKey[] = $key;
        }
        
        if (is_null($value)) {
            $value = 'NULL';
        } else if (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }
        
        $this->setVal[$index][] = $value;
        return $this;
    }
}
