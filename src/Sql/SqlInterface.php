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
interface SqlInterface
{   
    /**
     * Connects to the database
     *
     * @param array $options The connection options
     *
     * @return SqlInterface
     */
    public function connect(array $options = []);
    
    /**
     * Binds a value and returns the bound key
     *
     * @param *string|array|number|null $value What to bind
     *
     * @return string
     */
    public function bind($value);
    
    /**
     * Returns collection
     *
     * @param array $data Initial collection data
     *
     * @return Collection
     */
    public function collection(array $data = []);
    
    /**
     * Removes rows that match a filter
     *
     * @param *string|null $table   The table name
     * @param array|string $filters Filters to test against
     *
     * @return SqlInterface
     */
    public function deleteRows($table, $filters = null);
    
    /**
     * Returns all the bound values of this query
     *
     * @return array
     */
    public function getBinds();
    
    /**
     * Returns the connection object
     * if no connection has been made
     * it will attempt to make it
     *
     * @return resource PDO connection resource
     */
    public function getConnection();
    
    /**
     * Returns the last inserted id
     *
     * @param string|null $column A particular column name
     *
     * @return int the id
     */
    public function getLastInsertedId($column = null);
    
    /**
     * Returns a 1 row result given the column name and the value
     *
     * @param *string      $table Table name
     * @param *string      $name  Column name
     * @param *scalar|null $value Column value
     *
     * @return array|null
     */
    public function getRow($table, $name, $value);
    
    /**
     * Inserts data into a table and returns the ID
     *
     * @param *string    $table   Table name
     * @param *array     $setting Key/value array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return SqlInterface
     */
    public function insertRow($table, array $settings, $bind = true);
    
    /**
     * Inserts multiple rows into a table
     *
     * @param *string    $table   Table name
     * @param array      $setting Key/value 2D array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return SqlInterface
     */
    public function insertRows($table, array $settings, $bind = true);
    
    /**
     * Returns model
     *
     * @param array $data The initial data to set
     *
     * @return Model
     */
    public function model(array $data = []);
    
    /**
     * Queries the database
     *
     * @param *string $query The query to ran
     * @param array   $binds List of binded values
     *
     * @return array
     */
    public function query($query, array $binds = []);
    
    /**
     * Returns search
     *
     * @param string|null $table Table name
     *
     * @return Search
     */
    public function search($table = null);
    
    /**
     * Sets all the bound values of this query
     *
     * @param *array $binds key/values to bind
     *
     * @return SqlInterface
     */
    public function setBinds(array $binds);
    
    /**
     * Updates rows that match a filter given the update settings
     *
     * @param *string      $table   Table name
     * @param *array       $setting Key/value array matching table columns
     * @param array|string $filters Filters to test against
     * @param bool|array   $bind    Whether to compute with binded variables
     *
     * @return SqlInterface
     */
    public function updateRows($table, array $settings, $filters = null, $bind = true);
}
