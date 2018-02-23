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
 * Generates delete query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryDelete extends AbstractQuery
{
    /**
     * @var array $table Table name
     */
    protected $table = null;

    /**
     * @var array $where List of filters
     */
    protected $where = [];
    
    /**
     * Construct: Set the table, if any
     *
     * @param string|null $table The initial name of the table
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
        return 'DELETE FROM `'
            .$this->table.'` WHERE '
            .implode(' AND ', $this->where).';';
    }
    
    /**
     * Set the table name in which you want to delete from
     *
     * @param string|null $table The initial name of the table
     *
     * @return Delete
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
    
    /**
     * Where clause
     *
     * @param array|string $where The where clause
     *
     * @return Delete
     */
    public function where($where)
    {
        if (is_string($where)) {
            $where = [$where];
        }
        
        $this->where = array_merge($this->where, $where);
        
        return $this;
    }
}
