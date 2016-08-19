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

use Cradle\Sql\PostGreSql\QueryDelete as PostGreSqlQueryDelete;
use Cradle\Sql\PostGreSql\QueryInsert as PostGreSqlQueryInsert;
use Cradle\Sql\PostGreSql\QuerySelect as PostGreSqlQuerySelect;
use Cradle\Sql\PostGreSql\QueryUpdate as PostGreSqlQueryUpdate;
use Cradle\Sql\PostGreSql\QueryAlter;
use Cradle\Sql\PostGreSql\QueryCreate;
use Cradle\Sql\PostGreSql\QuerySubSelect;
use Cradle\Sql\PostGreSql\QueryUtility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a PostGreSql database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all Sql classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   Cradle
 * @package  PostGreSql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class PostGreSql extends AbstractSql implements SqlInterface
{
    /**
     * @var string $host Database host
     */
    protected $host = 'localhost';

    /**
     * @var string $port Database port
     */
    protected $port = '5432';

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
     * @return PostGreSql
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

        $host = $port = null;
        
        if (!is_null($this->host)) {
            $host = 'host='.$this->host.';';
            if (!is_null($this->port)) {
                $port = 'port='.$this->port.';';
            }
        }
        
        $connection = 'pgsql:'.$host.$port.'dbname='.$this->name;
        
        $this->connection = new PDO($connection, $this->user, $this->pass, $options);
        
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
     * Query for showing all columns of a table
     *
     * @param *string $table   The name of the table
     * @param array   $filters Where filters
     *
     * @return array
     */
    public function getColumns($table, $schema = null)
    {
        $select = [
            'columns.table_schema',
            'columns.column_name',
            'columns.ordinal_position',
            'columns.column_default',
            'columns.is_nullable',
            'columns.data_type',
            'columns.character_maximum_length',
            'columns.character_octet_length',
            'pg_class2.relname AS index_type'
        ];
        
        $where = [
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid'
        ];
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = $this
            ->getSelectQuery($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS    AS columns', $where[1], false)
            ->leftJoin('pg_index', $where[2], false)
            ->leftJoin('pg_class AS pg_class2', $where[3], false)
            ->getQuery();
        
        $results = $this->query($query);
        
        $columns = [];
        foreach ($results as $column) {
            $key = null;
            if (strpos($column['index_type'], '_pkey') !== false) {
                $key = 'PRI';
            } else if (strpos($column['index_type'], '_key') !== false) {
                $key = 'UNI';
            }
            
            $columns[] = [
                'Field'     => $column['column_name'],
                'Type'      => $column['data_type'],
                'Default'   => $column['column_default'],
                'Null'      => $column['is_nullable'],
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
     * Returns the delete query builder
     *
     * @param *string|null $table The table name
     *
     * @return QueryDelete
     */
    public function getDeleteQuery($table = null)
    {
        return $this->resolve(PostGreSqlQueryDelete::class, $table);
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string     $table  the name of the table
     * @param string|null $schema if from a particular schema
     *
     * @return array
     */
    public function getIndexes($table, $schema = null)
    {
        $select = [
            'columns.column_name',
            'pg_class2.relname AS index_type'
        ];
        
        $where = [
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid'
        ];
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = $this
            ->getSelectQuery($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS    AS columns', $where[1], false)
            ->innerJoin('pg_index', $where[2], false)
            ->innerJoin('pg_class AS pg_class2', $where[3], false)
            ->getQuery();
            
        return $this->query($query);
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
        return $this->resolve(PostGreSqlQueryInsert::class, $table);
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string     $table  the name of the table
     * @param string|null $schema if from a particular schema
     *
     * @return array
     */
    public function getPrimary($table, $schema = null)
    {
        $select = ['columns.column_name'];
        
        $where = [
            "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
            'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
            'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
            'pg_class2.oid = pg_index.indexrelid'];
        
        if ($schema) {
            $where[1] .= ' AND columns.table_schema="'.$schema.'"';
        }
        
        $query = $this
            ->getSelectQuery($select)
            ->from('pg_attribute')
            ->innerJoin('pg_class AS pg_class1', $where[0], false)
            ->innerJoin('information_schema.COLUMNS    AS columns', $where[1], false)
            ->innerJoin('pg_index', $where[2], false)
            ->innerJoin('pg_class AS pg_class2', $where[3], false)
            ->where('pg_class2.relname LIKE \'%_pkey\'')
            ->getQuery();
        
        return $this->query($query);
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
        return $this->resolve(PostGreSqlQuerySelect::class, $select);
    }
    
    /**
     * Returns a listing of tables in the DB
     *
     * @return array|false
     */
    public function getTables()
    {
        $query = $this
            ->getSelectQuery('tablename')
            ->from('pg_tables')
            ->where("tablename NOT LIKE 'pg\\_%'")
            ->where("tablename NOT LIKE 'sql\\_%'")
            ->getQuery();
        
        return $this->query($query);
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
        return $this->resolve(PostGreSqlQueryUpdate::class, $table);
    }
    
    /**
     * Set schema search paths
     *
     * @param string $schema Schema name
     *
     * @return Index
     */
    public function setSchema($schema)
    {
        $schema = func_get_args();
        
        $schema = "'".implode("','", $schema)."'";
        
        $query = $this->getUtilityQuery()->setSchema($schema);
        $this->query($query);
        
        return $this;
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
