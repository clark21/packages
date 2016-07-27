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
class QuerySelect extends AbstractQuery
{
    /**
     * @var string|null $select List of columns
     */
    protected $select   = null;

    /**
     * @var string|null $from Main table
     */
    protected $from = null;

    /**
     * @var array|null $joins List of relatoinal joins
     */
    protected $joins = null;

    /**
     * @var array $where List of filters
     */
    protected $where = [];

    /**
     * @var array $sortBy List of order and directions
     */
    protected $sortBy = [];

    /**
     * @var array $group List of "group bys"
     */
    protected $group = [];

    /**
     * @var int|null $page Pagination start
     */
    protected $page = null;

    /**
     * @var int|null $length Pagination range
     */
    protected $length = null;
    
    /**
     * Construct: Set the columns, if any
     *
     * @param string|null $select Column names
     */
    public function __construct($select = '*')
    {
        $this->select($select);
    }
    
    /**
     * From clause
     *
     * @param *string $from Main table
     *
     * @return Select
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        $joins = empty($this->joins) ? '' : implode(' ', $this->joins);
        $where = empty($this->where) ? '' : 'WHERE '.implode(' AND ', $this->where);
        $sort = empty($this->sortBy) ? '' : 'ORDER BY '.implode(', ', $this->sortBy);
        $limit = is_null($this->page) ? '' : 'LIMIT ' . $this->page .',' .$this->length;
        $group = empty($this->group) ? '' : 'GROUP BY ' . implode(', ', $this->group);
        
        $query = sprintf(
            'SELECT %s FROM %s %s %s %s %s %s;',
            $this->select,
            $this->from,
            $joins,
            $where,
            $group,
            $sort,
            $limit
        );
        
        return str_replace('  ', ' ', $query);
    }
    
    /**
     * Group by clause
     *
     * @param *string|array $group List of "group bys"
     *
     * @return Select
     */
    public function groupBy($group)
    {
        if (is_string($group)) {
            $group = [$group];
        }
        
        $this->group = $group;
        return $this;
    }
    
    /**
     * Inner join clause
     *
     * @param *string $table Table name to join
     * @param *string $where Filter/s
     * @param bool    $using Whether to use "using" syntax (as opposed to "on")
     *
     * @return Select
     */
    public function innerJoin($table, $where, $using = true)
    {
        return $this->join('INNER', $table, $where, $using);
    }
    
    /**
     * Allows you to add joins of different types
     * to the query
     *
     * @param *string $type  Join type
     * @param *string $table Table name to join
     * @param *string $where Filter/s
     * @param bool    $using Whether to use "using" syntax (as opposed to "on")
     *
     * @return Select
     */
    public function join($type, $table, $where, $using = true)
    {
        $linkage = $using ? 'USING ('.$where.')' : ' ON ('.$where.')';
        $this->joins[] = $type.' JOIN ' . $table . ' ' . $linkage;
        
        return $this;
    }
    
    /**
     * Left join clause
     *
     * @param *string $table Table name to join
     * @param *string $where Filter/s
     * @param bool    $using Whether to use "using" syntax (as opposed to "on")
     *
     * @return Select
     */
    public function leftJoin($table, $where, $using = true)
    {
        return $this->join('LEFT', $table, $where, $using);
    }
    
    /**
     * Limit clause
     *
     * @param *string|int $page   Pagination start
     * @param *string|int $length Pagination range
     *
     * @return Select
     */
    public function limit($page, $length)
    {
        $this->page = $page;
        $this->length = $length;

        return $this;
    }
    
    /**
     * Outer join clause
     *
     * @param *string $table Table name to join
     * @param *string $where Filter/s
     * @param bool    $using Whether to use "using" syntax (as opposed to "on")
     *
     * @return Select
     */
    public function outerJoin($table, $where, $using = true)
    {
        return $this->join('OUTER', $table, $where, $using);
    }
    
    /**
     * Right join clause
     *
     * @param *string $table Table name to join
     * @param *string $where Filter/s
     * @param bool    $using Whether to use "using" syntax (as opposed to "on")
     *
     * @return Select
     */
    public function rightJoin($table, $where, $using = true)
    {
        return $this->join('RIGHT', $table, $where, $using);
    }
    
    /**
     * Select clause
     *
     * @param string $select Select columns
     *
     * @return Select
     */
    public function select($select = '*')
    {
        //if select is an array
        if (is_array($select)) {
            //transform into a string
            $select = implode(', ', $select);
        }
        
        $this->select = $select;
        
        return $this;
    }
    
    /**
     * Order by clause
     *
     * @param *string $field Column name
     * @param string  $order Direction
     *
     * @return Select
     */
    public function sortBy($field, $order = 'ASC')
    {
        $this->sortBy[] = $field . ' ' . $order;
        
        return $this;
    }
    
    /**
     * Where clause
     *
     * @param array|string Filter/s
     *
     * @return Select
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
