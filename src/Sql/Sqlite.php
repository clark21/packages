<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql;

use PDO;

use Cradle\Sql\Sqlite\QueryAlter;
use Cradle\Sql\Sqlite\QueryCreate;
use Cradle\Sql\Sqlite\QueryUtility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a Sqlite database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all Sql classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   Cradle
 * @package  Sqlite
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Sqlite extends AbstractSql implements SqlInterface
{
    /**
     * @var string $path Sqlite file path
     */
    protected $path = null;
    
    /**
     * Construct: Store connection information
     *
     * @param *string $path Sqlite file path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    /**
     * Connects to the database
     *
     * @param PDO|array $options The connection options
     *
     * @return Sqlite
     */
    public function connect($options = [])
    {
        if ($options instanceof PDO) {
            $this->connection = $options;
            return $this;
        }
        
        if (!is_array($options)) {
            $options = array();
        }

        $this->connection = new PDO('sqlite:'.$this->path);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->trigger('connect');
        
        return $this;
    }
    
    /**
     * Returns the alter query builder
     *
     * @param *string $name Name of table
     *
     * @return QueryAlter
     */
    public function getAlterQuery($name = null)
    {
        return $this->resolve(QueryAlter::class, $name);
    }
    
    /**
     * Returns the columns and attributes given the table name
     *
     * @param *string $table The name of the table
     *
     * @return array|false
     */
    public function getColumns($table)
    {
        $query = $this->getUtilityQuery()->showColumns($table);

        $results = $this->query($query, $this->getBinds());
        
        $columns = [];
        foreach ($results as $column) {
            $key = null;
            if ($column['pk'] == 1) {
                $key = 'PRI';
            }
            
            $columns[] = [
                'Field'     => $column['name'],
                'Type'      => $column['type'],
                'Default'   => $column['dflt_value'],
                'Null'      => $column['notnull'] != 1,
                'Key'       => $key
            ];
        }
        
        return $columns;
    }
    
    /**
     * Returns the create query builder
     *
     * @param *string $name Name of table
     *
     * @return QueryCreate
     */
    public function getCreateQuery($name = null)
    {
        return $this->resolve(QueryCreate::class, $name);
    }
    
    /**
     * Peturns the primary key name given the table
     *
     * @param *string $table The table name
     *
     * @return string
     */
    public function getPrimaryKey($table)
    {
        $results = $this->getColumns($table, "`Key` = 'PRI'");
        return isset($results[0]['Field']) ? $results[0]['Field'] : null;
    }
    
    /**
     * Returns a listing of tables in the DB
     *
     * @param string|null $like The like pattern
     *
     * @return array|false
     */
    public function getTables($like = null)
    {
        $query = $this->getUtilityQuery();
        $like = $like ? $this->bind($like) : null;
        return $this->query($query->showTables($like), $this->getBinds());
    }
    
    /**
     * Inserts multiple rows into a table
     *
     * @param *string    $table   Table name
     * @param array      $setting Key/value 2D array matching table columns
     * @param bool|array $bind    Whether to compute with binded variables
     *
     * @return Sqlite
     */
    public function insertRows($table, array $settings, $bind = true)
    {
        //this is an array of arrays
        foreach ($settings as $index => $setting) {
            //Sqlite no available multi insert
            //there's work arounds, but no performance gain
            $this->insertRow($table, $setting, $bind);
        }
        
        return $this;
    }
    
    /**
     * Returns the select query builder
     *
     * @param string|array $select Column list
     *
     * @return Sqlite
     */
    public function getSelectQuery($select = 'ROWID,*')
    {
        return parent::getSelectQuery($select);
    }
    
    /**
     * Returns the alter query builder
     *
     * @return QueryUtility
     */
    public function getUtilityQuery()
    {
        return $this->resolve(QueryUtility::class);
    }
}
