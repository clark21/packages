<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql;

use Cradle\Event\EventTrait;

use Cradle\Helper\InstanceTrait;
use Cradle\Helper\LoopTrait;
use Cradle\Helper\ConditionalTrait;

use Cradle\Profiler\InspectorTrait;
use Cradle\Profiler\LoggerTrait;

use Cradle\Resolver\StateTrait;
use Cradle\Resolver\ResolverException;

/**
 * Sql Search
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Search
{
    use EventTrait,
        InstanceTrait,
        LoopTrait,
        ConditionalTrait,
        InspectorTrait,
        LoggerTrait,
        StateTrait;

    /**
     * @const string LEFT Join type
     */
    const LEFT  = 'LEFT';

    /**
     * @const string RIGHT Join type
     */
    const RIGHT = 'RIGHT';

    /**
     * @const string INNER Join type
     */
    const INNER = 'INNER';

    /**
     * @const string OUTER Join type
     */
    const OUTER = 'OUTER';

    /**
     * @const string ASC Sort direction
     */
    const ASC   = 'ASC';

    /**
     * @const string DESC Sort direction
     */
    const DESC  = 'DESC';

    /**
     * @var SqlInterface|null $database Database object
     */
    protected $database = null;

    /**
     * @var string|null $table Table name
     */
    protected $table = null;

    /**
     * @var array $columns List of columns
     */
    protected $columns = [];

    /**
     * @var array $join List of relational joins
     */
    protected $join = [];

    /**
     * @var array $filter List of filters
     */
    protected $filter = [];

    /**
     * @var array $sort List of orders and directions
     */
    protected $sort = [];

    /**
     * @var array $group List of "group bys"
     */
    protected $group = [];

    /**
     * @var array $start Pagination start
     */
    protected $start = 0;

    /**
     * @var array $range Pagination range
     */
    protected $range = 0;

    /**
     * Magical processing of sortBy
     * and filterBy Methods
     *
     * @param *string $name Name of method
     * @param *array  $args Arguments to pass
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        //if method starts with filterBy
        if (strpos($name, 'filterBy') === 0) {
            //ex. filterByUserName('Chris', '-')
            //choose separator
            $separator = '_';
            if (isset($args[1]) && is_scalar($args[1])) {
                $separator = (string) $args[1];
            }

            //transform method to column name
            $key = substr($name, 8);
            $key = preg_replace("/([A-Z0-9])/", $separator."$1", $key);
            $key = substr($key, strlen($separator));
            $key = strtolower($key);

            //if arg isn't set
            if (!isset($args[0])) {
                //default is null
                $args[0] = null;
            }

            //generate key
            if (is_array($args[0])) {
                $key = $key.' IN %s';
            } else {
                $key = $key.'=%s';
            }

            //add it to the search filter
            $this->addFilter($key, $args[0]);

            return $this;
        }

        //if method starts with sortBy
        if (strpos($name, 'sortBy') === 0) {
            //ex. sortByUserName('Chris', '-')
            //determine separator
            $separator = '_';
            if (isset($args[1]) && is_scalar($args[1])) {
                $separator = (string) $args[1];
            }

            //transform method to column name
            $key = substr($name, 6);
            $key = preg_replace("/([A-Z0-9])/", $separator."$1", $key);
            $key = substr($key, strlen($separator));
            $key = strtolower($key);

            //if arg isn't set
            if (!isset($args[0])) {
                //default is null
                $args[0] = null;
            }

            //add it to the search sort
            $this->addSort($key, $args[0]);

            return $this;
        }

        try {
            return $this->__callResolver($name, $args);
        } catch (ResolverException $e) {
            throw new SqlException($e->getMessage());
        }
    }

    /**
     * Construct: Store database
     *
     * @param SqlInterface $database Database object
     */
    public function __construct(SqlInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Adds filter
     *
     * @param *string           sprintf format
     * @param string[,string..] sprintf values
     *
     * @return Search
     */
    public function addFilter()
    {
        $this->filter[] = func_get_args();

        return $this;
    }

    /**
     * Adds sort
     *
     * @param *string $column Column name
     * @param string  $order  ASC or DESC
     *
     * @return Search
     */
    public function addSort($column, $order = self::ASC)
    {
        if ($order != self::DESC) {
            $order = self::ASC;
        }

        $this->sort[$column] = $order;

        return $this;
    }

    /**
     * Returns the results in a collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->resolve(Collection::class)
            ->setDatabase($this->database)
            ->setTable($this->table)
            ->set($this->getRows());
    }

    /**
     * Returns the one result in a model
     *
     * @param int $index Row index to return
     *
     * @return Model
     */
    public function getModel($index = 0)
    {
        return $this->getCollection()->offsetGet($index);
    }

    /**
     * Returns the one result
     *
     * @param int         $index  Row index to return
     * @param string|null $column Specific column to return
     *
     * @return array|null
     */
    public function getRow($index = 0, $column = null)
    {
        if (is_string($index)) {
            $column = $index;
            $index = 0;
        }

        $rows = $this->getRows();

        if (!is_null($column) && isset($rows[$index][$column])) {
            return $rows[$index][$column];
        } else if (is_null($column) && isset($rows[$index])) {
            return $rows[$index];
        }

        return null;
    }

    /**
     * Returns the array rows
     *
     * @param callable|null $callback
     *
     * @return array
     */
    public function getRows($callback = null)
    {
        $query = $this->getQuery();

        if (!empty($this->columns)) {
            $query->select(implode(', ', $this->columns));
        }

        foreach ($this->sort as $key => $value) {
            $query->sortBy($key, $value);
        }

        if ($this->range) {
            $query->limit($this->start, $this->range);
        }

        if (!empty($this->group)) {
            $query->groupBy($this->group);
        }

        if (!empty($this->having)) {
            $query->having($this->having);
        }

        $rows = $this->database->query($query, $this->database->getBinds(), $callback);

        if (!$callback) {
            return $rows;
        }

        return $this;
    }

    /**
     * Group by clause
     *
     * @param string $group Column name
     *
     * @return Search
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
     * Returns the total results
     *
     * @return int
     */
    public function getTotal()
    {
        $query = $this->getQuery()->select('COUNT(*) as total');

        $rows = $this->database->query($query, $this->database->getBinds());

        if (!isset($rows[0]['total'])) {
            return 0;
        }

        return $rows[0]['total'];
    }

    /**
     * Having clause
     *
     * @param string $having Column name
     *
     * @return Search
     */
    public function having($having)
    {
        if (is_string($having)) {
            $having = [$having];
        }

        $this->having = $having;
        return $this;
    }

    /**
     * Adds Inner Join On
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function innerJoinOn($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::INNER, $table, $where, false];

        return $this;
    }

    /**
     * Adds Inner Join Using
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function innerJoinUsing($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::INNER, $table, $where, true];

        return $this;
    }

    /**
     * Adds Left Join On
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function leftJoinOn($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::LEFT, $table, $where, false];

        return $this;
    }

    /**
     * Adds Left Join Using
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function leftJoinUsing($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::LEFT, $table, $where, true];

        return $this;
    }

    /**
     * Adds Outer Join On
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function outerJoinOn($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::OUTER, $table, $where, false];

        return $this;
    }

    /**
     * Adds Outer Join USing
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function outerJoinUsing($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::OUTER, $table, $where, true];

        return $this;
    }

    /**
     * Adds Right Join On
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function rightJoinOn($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::RIGHT, $table, $where, false];

        return $this;
    }

    /**
     * Adds Right Join Using
     *
     * @param *string            $table Table name
     * @param *string[,string..] $where Filter/s
     *
     * @return Search
     */
    public function rightJoinUsing($table, $where)
    {
        $where = func_get_args();
        $table = array_shift($where);

        $this->join[] = [self::RIGHT, $table, $where, true];

        return $this;
    }

    /**
     * Sets Columns
     *
     * @param string[,string..]|array $columns List of table columns
     *
     * @return Search
     */
    public function setColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * Sets the pagination page
     *
     * @param int $page Pagination page
     *
     * @return Search
     */
    public function setPage($page)
    {
        if ($page < 1) {
            $page = 1;
        }

        if ($this->range == 0) {
            $this->setRange(25);
        }

        $this->start = ($page - 1) * $this->range;

        return $this;
    }

    /**
     * Sets the pagination range
     *
     * @param int $range Pagination range
     *
     * @return Search
     */
    public function setRange($range)
    {
        if ($range < 0) {
            $range = 25;
        }

        $this->range = $range;

        return $this;
    }

    /**
     * Sets the pagination start
     *
     * @param int $start Pagination start
     *
     * @return Search
     */
    public function setStart($start)
    {
        if ($start < 0) {
            $start = 0;
        }

        $this->start = $start;

        return $this;
    }

    /**
     * Sets Table
     *
     * @param string $table Table class name
     *
     * @return Search
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Builds query based on the data given
     *
     * @return string
     */
    protected function getQuery()
    {
        $query = $this->database->getSelectQuery()->from($this->table);

        foreach ($this->join as $join) {
            if (!is_array($join[2])) {
                $join[2] = [$join[2]];
            }

            $where = array_shift($join[2]);
            if (!empty($join[2])) {
                foreach ($join[2] as $i => $value) {
                    $join[2][$i] = $this->database->bind($value);
                }

                $where = vsprintf($where, $join[2]);
            }

            $query->join($join[0], $join[1], $where, $join[3]);
        }

        foreach ($this->filter as $i => $filter) {
            //array('post_id=%s AND post_title IN %s', 123, array('asd'));
            $where = array_shift($filter);
            if (!empty($filter)) {
                foreach ($filter as $i => $value) {
                    $filter[$i] = $this->database->bind($value);
                }

                $where = vsprintf($where, $filter);
            }

            $query->where($where);
        }

        return $query;
    }
}
