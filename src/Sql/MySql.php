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

use Cradle\Sql\MySql\QueryAlter;
use Cradle\Sql\MySql\QueryCreate;
use Cradle\Sql\MySql\QuerySubSelect;
use Cradle\Sql\MySql\QueryUtility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a MySql database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all Sql classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class MySql extends AbstractSql implements SqlInterface
{
    /**
     * @var string $host Database host
     */
    protected $host = 'localhost';

    /**
     * @var string|null $name Database name
     */
    protected $name = null;

    /**
     * @var string|null $user Database user name
     */
    protected $user = null;

    /**
     * @var string|null $pass Database password
     */
    protected $pass = null;
    
    /**
     * Construct: Store connection information
     *
     * @param *string      $host Database host
     * @param *string|null $name Database name
     * @param *string|null $user Database user name
     * @param string|null  $pass Database password
     * @param number|null  $port Database port
     */
    public function __construct($host, $name, $user, $pass = null, $port = null)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }
    
    /**
     * Connects to the database
     *
     * @param PDO|array $options the connection options
     *
     * @return MySql
     */
    public function connect($options = [])
    {
		if($options instanceof PDO) {
			$this->connection = $options;
			return $this;
		}
		
		if(!is_array($options)) {
			$options = array();
		}
		
        $host = $port = null;
        
        if (!is_null($this->host)) {
            $host = 'host='.$this->host.';';
            if (!is_null($this->port)) {
                $port = 'port='.$this->port.';';
            }
        }
        
        $connection = 'mysql:'.$host.$port.'dbname='.$this->name;
        
        $this->connection = new PDO($connection, $this->user, $this->pass, $options);
        
        $this->trigger('mysql-connect');
        
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
     * @param string $table   The name of the table
     * @param array  $filters Where filters
     *
     * @return array|false
     */
    public function getColumns($table, $filters = null)
    {
        $query = $this->getUtilityQuery();
        
        if (is_array($filters)) {
            foreach ($filters as $i => $filter) {
                //array('post_id=%s AND post_title IN %s', 123, array('asd'));
                $format = array_shift($filter);
                $filter = $this->bind($filter);
                $filters[$i] = vsprintf($format, $filter);
            }
        }
        
        $query->showColumns($table, $filters);
        return $this->query($query, $this->getBinds());
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
     * @param string $table Table name
     *
     * @return string
     */
    public function getPrimaryKey($table)
    {
        $query = $this->getUtilityQuery();
        $results = $this->getColumns($table, "`Key` = 'PRI'");
        return isset($results[0]['Field']) ? $results[0]['Field'] : null;
    }
    
    /**
     * Returns the whole enitre schema and rows
     * of the current databse
     *
     * @return string
     */
    public function getSchema()
    {
        $backup = [];
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $backup[] = $this->getBackup();
        }
        
        return implode("\n\n", $backup);
    }
    
    /**
     * Returns the Subselect query builder
     *
     * @param string $parentQuery The parent query
     * @param string $select      List of columns
     *
     * @return QuerySubSelect
     */
    public function getSubSelectQuery($parentQuery, $select = '*')
    {
        return $this->resolve(QuerySubSelect::class, $parentQuery, $select);
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
        $results = $this->query($query->showTables($like), $this->getBinds());
        $newResults = [];

        foreach ($results as $result) {
            foreach ($result as $key => $value) {
                $newResults[] = $value;
                break;
            }
        }
        
        return $newResults;
    }
    
    /**
     * Returns the whole enitre schema and rows
     * of the current table
     *
     * @param *string $table Name of table
     *
     * @return string
     */
    public function getTableSchema($table)
    {
        $backup = [];
        //get the schema
        $schema = $this->getColumns($table);
        if (count($schema)) {
            //lets rebuild this schema
            $query = $this->getCreateQuery()->setName($table);

            foreach ($schema as $field) {
                //first try to parse what we can from each field
                $fieldTypeArray = explode(' ', $field['Type']);
                $typeArray = explode('(', $fieldTypeArray[0]);
                
                $type = $typeArray[0];
                $length = str_replace(')', '', $typeArray[1]);
                $attribute = isset($fieldTypeArray[1]) ? $fieldTypeArray[1] : null;
                
                $null = strtolower($field['Null']) == 'no' ? false : true;
                
                $increment = strtolower($field['Extra']) == 'auto_increment' ? true : false;
                
                //lets now add a field to our schema class
                $query->addField($field['Field'], [
                    'type'              => $type,
                    'length'            => $length,
                    'attribute'         => $attribute,
                    'null'              => $null,
                    'default'           => $field['Default'],
                    'auto_increment'    => $increment
				]);
                
                //set keys where found
                switch ($field['Key']) {
                    case 'PRI':
                        $query->addPrimaryKey($field['Field']);
                        break;
                    case 'UNI':
                        $query->addUniqueKey($field['Field'], [$field['Field']]);
                        break;
                    case 'MUL':
                        $query->addKey($field['Field'], [$field['Field']]);
                        break;
                }
            }
            
            //store the query but dont run it
            $backup[] = $query;
        }
        
        //get the rows
        $rows = $this->query($this->getSelectQuery()->from($table)->getQuery());
        
        if (count($rows)) {
            //lets build an insert query
            $query = $this->getInsertQuery($table);

            foreach ($rows as $index => $row) {
                foreach ($row as $key => $value) {
                    $query->set($key, $this->getBinds($value), $index);
                }
            }
            
            //store the query but dont run it
            $backup[] = $query->getQuery(true);
        }
        
        return implode("\n\n", $backup);
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
