<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql;

use StdClass;
use PDO;

use Cradle\Event\EventTrait;

use Cradle\Helper\InstanceTrait;
use Cradle\Helper\LoopTrait;
use Cradle\Helper\ConditionalTrait;

use Cradle\Profiler\CallerTrait;
use Cradle\Profiler\InspectorTrait;
use Cradle\Profiler\LoggerTrait;

use Cradle\Resolver\StateTrait;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a database. This class also lays out
 * query building methods that auto renders a valid query
 * the specific database will understand without actually
 * needing to know the query language.
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
abstract class AbstractSql
{
	use EventTrait, 
		InstanceTrait, 
		LoopTrait, 
		ConditionalTrait, 
		CallerTrait, 
		InspectorTrait, 
		LoggerTrait, 
		StateTrait
		{
			StateTrait::__callResolver as __call;
		}

    /**
     * @const int INSTANCE Flag that designates multiton when using ::i()
     */
    const INSTANCE = 0;

    /**
     * @const string FIRST The first index in getQueries
     */
    const FIRST = 'first';

    /**
     * @const string LAST The last index in getQueries
     */
    const LAST = 'last';
       
    /**
     * @var [RESOURCE] $connection PDO resource
     */
    protected $connection = null;
       
    /**
     * @var array $binds Bound data from the current query
     */
    protected $binds = [];
    
    /**
     * Connects to the database
     *
     * @param array $options The connection options
     *
     * @return AbstractSQL
     */
    abstract public function connect(array $options = []);
    
    /**
     * Binds a value and returns the bound key
     *
     * @param *string|array|number|null $value What to bind
     *
     * @return string
     */
    public function bind($value)
    {
        if (is_array($value)) {
            foreach ($value as $i => $item) {
                $value[$i] = $this->bind($item);
            }
            
            return '('.implode(",", $value).')';
        } else if (is_int($value) || ctype_digit($value)) {
            return $value;
        }
        
        $name = ':bind'.count($this->binds).'bind';
        $this->binds[$name] = $value;
        return $name;
    }
    
    /**
     * Returns collection
     *
     * @param array $data Initial collection data
     *
     * @return Collection
     */
    public function collection(array $data = [])
    {
		return $this
			->resolve(Collection::class)
			->setDatabase($this)
			->set($data);
    }
    
    /**
     * Removes rows that match a filter
     *
     * @param *string|null $table   The table name
     * @param array|string $filters Filters to test against
     *
     * @return AbstractSql
     */
    public function deleteRows($table, $filters = null)
    {
        $query = $this->getDeleteQuery($table);
        
        //array('post_id=%s AND post_title IN %s', 123, array('asd'));
        if (is_array($filters)) {
            //can be array of arrays
            if (is_array($filters[0])) {
                foreach ($filters as $i => $filter) {
                    if (is_array($filters)) {
                        $format = array_shift($filter);
                        
                        //reindex filters
                        $filter = array_values($filter);
                        
                        //bind filters
                        foreach ($filter as $i => $value) {
                            $filter[$i] = $this->bind($value);
                        }
                        
                        //combine
                        $query->where(vsprintf($format, $filter));
                    }
                }
            } else {
                $format = array_shift($filters);
                
                //reindex filters
                $filters = array_values($filters);
                
                //bind filters
                foreach ($filters as $i => $value) {
                    $filters[$i] = $this->bind($value);
                }
                
                //combine
                $query->where(vsprintf($format, $filters));
            }
        } else {
            $query->where($filters);
        }
        
        //run the query
        $this->query($query, $this->getBinds());
        
        //trigger event
        $this->trigger('sql-delete', $table, $filters);
        
        return $this;
    }
    
    /**
     * Returns all the bound values of this query
     *
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }
    
    /**
     * Returns the connection object
     * if no connection has been made
     * it will attempt to make it
     *
     * @return resource PDO connection resource
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Returns the delete query builder
     *
     * @param *string|null $table The table name
     *
     * @return QueryDelete
     */
    public function getDeleteQuery($table = null)
    {
        return $this->resolve(QueryDelete::class, $table);
    }
    
    /**
     * Returns the insert query builder
     *
     * @param string|null $table Name of table
     *
     * @return QueryInsert
     */
    public function getInsertQuery($table = null)
    {
        return $this->resolve(QueryInsert::class, $table);
    }
    
    /**
     * Returns the last inserted id
     *
     * @param string|null $column A particular column name
     *
     * @return int the id
     */
    public function getLastInsertedId($column = null)
    {
        if (is_string($column)) {
            return $this->getConnection()->lastInsertId($column);
        }
        
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Returns a model given the column name and the value
     *
     * @param *string      $table Table name
     * @param *string      $name  Column name
     * @param *scalar|null $value Column value
     *
     * @return Model|null
     */
    public function getModel($table, $name, $value)
    {
        //get the row
        $result = $this->getRow($table, $name, $value);
        
        if (is_null($result)) {
            return null;
        }
        
        return $this->model()->setTable($table)->set($result);
    }
    
    /**
     * Returns a 1 row result given the column name and the value
     *
     * @param *string      $table Table name
     * @param *string      $name  Column name
     * @param *scalar|null $value Column value
     *
     * @return array|null
     */
    public function getRow($table, $name, $value)
    {
        //make the query
        $query = $this
			->getSelectQuery()
            ->from($table)
            ->where($name.' = '.$this->bind($value))
            ->limit(0, 1);
        
        //get the results
        $results = $this->query($query, $this->getBinds());
        
        //event trigger
        $this->trigger('sql-row', $table, $name, $value, $results);
        
        //if we have results
        if (isset($results[0])) {
            //return it
            return $results[0];
        }
        
        return null;
    }
    
    /**
     * Returns the select query builder
     *
     * @param string|array $select Column list
     *
     * @return QuerySelect
     */
    public function getSelectQuery($select = '*')
    {
		return $this->resolve(QuerySelect::class, $select);
    }
    
    /**
     * Returns the update query builder
     *
     * @param string|null $table Name of table
     *
     * @return QueryUpdate
     */
    public function getUpdateQuery($table = null)
    {
        return $this->resolve(QueryUpdate::class, $table);
    }
    
    /**
     * Inserts data into a table and returns the ID
     *
     * @param *string    $table   Table name
     * @param *array     $setting Key/value array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return AbstractSql
     */
    public function insertRow($table, array $settings, $bind = true)
    {
        //build insert query
        $query = $this->getInsertQuery($table);
        
        //foreach settings
        foreach ($settings as $key => $value) {
            //if value is not a vulnerability
            if (is_null($value) || is_bool($value)) {
                //just add it to the query
                $query->set($key, $value);
                continue;
            }
            
            //if bind is true or is an array and we want to bind it
            if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
                //bind the value
                $value = $this->bind($value);
            }
            
            //add it to the query
            $query->set($key, $value);
        }
        
        //run the query
        $this->query($query, $this->getBinds());
        
        //event trigger
        $this->trigger('sql-insert', $table, $settings);
        
        return $this;
    }
    
    /**
     * Inserts multiple rows into a table
     *
     * @param *string    $table   Table name
     * @param array      $setting Key/value 2D array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return AbstractSql
     */
    public function insertRows($table, array $settings, $bind = true)
    {
        //build insert query
        $query = $this->getInsertQuery($table);
        
        //this is an array of arrays
        foreach ($settings as $index => $setting) {
            //for each column
            foreach ($setting as $key => $value) {
                //if value is not a vulnerability
                if (is_null($value) || is_bool($value)) {
                    //just add it to the query
                    $query->set($key, $value, $index);
                    continue;
                }
                
                //if bind is true or is an array and we want to bind it
                if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
                    //bind the value
                    $value = $this->bind($value);
                }
                
                //add it to the query
                $query->set($key, $value, $index);
            }
        }
        
        //run the query
        $this->query($query, $this->getBinds());
        
        $this->trigger('sql-inserts', $table, $settings);
        
        return $this;
    }
    
    /**
     * Returns model
     *
     * @param array $data The initial data to set
     *
     * @return Model
     */
    public function model(array $data = [])
    {
		return $this->resolve(Model::class, $data)->setDatabase($this);
    }
    
    /**
     * Queries the database
     *
     * @param *string $query The query to ran
     * @param array   $binds List of binded values
     *
     * @return array
     */
    public function query($query, array $binds = [])
    {
        $request = new StdClass();
        
        $request->query = $query;
        $request->binds = $binds;
        
        $connection = $this->getConnection();
        $query      = (string) $request->query;
        $stmt       = $connection->prepare($query);
        
        //bind some more values
        foreach ($request->binds as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        //PDO Execute
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            
            //unpack binds for the report
            foreach ($binds as $key => $value) {
                $query = str_replace($key, "'$value'", $query);
            }
            
            //throw Exception
			throw SqlException::forQueryError($query, $error[2]);
        }
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        //log query
        $this->log([
            'query'     => $query,
            'binds'     => $binds,
            'results'   => $results
		]);
        
        //clear binds
        $this->binds = [];
        
        return $results;
    }
    
    /**
     * Returns search
     *
     * @param string|null $table Table name
     *
     * @return Search
     */
    public function search($table = null)
    {
        $search = $this->resolve(Search::class, $this);
        
        if ($table) {
            $search->setTable($table);
        }
        
        return $search;
    }
    
    /**
     * Sets all the bound values of this query
     *
     * @param *array $binds key/values to bind
     *
     * @return AbstractSql
     */
    public function setBinds(array $binds)
    {
        $this->binds = $binds;
        return $this;
    }
    
    /**
     * Sets only 1 row given the column name and the value
     *
     * @param *string      $table   Table name
     * @param *string      $name    Column name
     * @param *scalar|null $value   Column value
     * @param *array       $setting Key/value array matching table columns
     *
     * @return AbstractSql
     */
    public function setRow($table, $name, $value, array $setting)
    {
        //first check to see if the row exists
        $row = $this->getRow($table, $name, $value);
        
        if (!$row) {
            //we need to insert
            $setting[$name] = $value;
            return $this->insertRow($table, $setting);
        }
        
        //we need to update this row
        return $this->updateRows($table, $setting, [$name.'=%s', $value]);
    }
    
    /**
     * Updates rows that match a filter given the update settings
     *
     * @param *string      $table   Table name
     * @param *array       $setting Key/value array matching table columns
     * @param array|string $filters Filters to test against
     * @param bool|array   $bind    Whether to compute with binded variables
     *
     * @return AbstractSql
     */
    public function updateRows($table, array $settings, $filters = null, $bind = true)
    {
        //build the query
        $query = $this->getUpdateQuery($table);
        
        //foreach settings
        foreach ($settings as $key => $value) {
            //if value is not a vulnerability
            if (is_null($value) || is_bool($value)) {
                //just add it to the query
                $query->set($key, $value);
                continue;
            }
            
            //if bind is true or is an array and we want to bind it
            if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
                //bind the value
                $value = $this->bind($value);
            }
            
            //add it to the query
            $query->set($key, $value);
        }
        
        //array('post_id=%s AND post_title IN %s', 123, array('asd'));
        if (is_array($filters)) {
            //can be array of arrays
            if (is_array($filters[0])) {
                foreach ($filters as $i => $filter) {
                    if (is_array($filters)) {
                        $format = array_shift($filter);
                        
                        //reindex filters
                        $filter = array_values($filter);
                        
                        //bind filters
                        foreach ($filter as $i => $value) {
                            $filter[$i] = $this->bind($value);
                        }
                        
                        //combine
                        $query->where(vsprintf($format, $filter));
                    }
                }
            } else {
                $format = array_shift($filters);
                
                //reindex filters
                $filters = array_values($filters);
                
                //bind filters
                foreach ($filters as $i => $value) {
                    $filters[$i] = $this->bind($value);
                }
                
                //combine
                $query->where(vsprintf($format, $filters));
            }
        } else {
            $query->where($filters);
        }
        
        //run the query
        $this->query($query, $this->getBinds());
        
        //event trigger
        $this->trigger('sql-update', $table, $settings, $filters);
        
        return $this;
    }
}
