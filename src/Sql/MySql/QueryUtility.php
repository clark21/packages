<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql\MySql;

use Cradle\Sql\AbstractQuery;

/**
 * Generates utility query strings
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryUtility extends AbstractQuery
{
    /**
     * @var string|null $query The query string
     */
    protected $query = null;
    
    /**
     * Query for dropping a table
     *
     * @param *string $table The name of the table
     *
     * @return QueryUtility
     */
    public function dropTable($table)
    {
        $this->query = 'DROP TABLE `' . $table .'`';
        return $this;
    }
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query.';';
    }
    
    /**
     * Query for renaming a table
     *
     * @param *string $table The name of the table
     * @param *string $name  The new name of the table
     *
     * @return QueryUtility
     */
    public function renameTable($table, $name)
    {
        $this->query = 'RENAME TABLE `' . $table . '` TO `' . $name . '`';
        return $this;
    }
    
    /**
     * Query for showing all columns of a table
     *
     * @param *string      $table The name of the table
     * @param *string|null $where Filter/s
     *
     * @return QueryUtility
     */
    public function showColumns($table, $where = null)
    {
        $where = $where ? ' WHERE '.$where : null;
        $this->query = 'SHOW FULL COLUMNS FROM `' . $table .'`' . $where;
        return $this;
    }
    
    /**
     * Query for showing all tables
     *
     * @param string|null $like The like pattern
     *
     * @return QueryUtility
     */
    public function showTables($like = null)
    {
        $like = $like ? ' LIKE '.$like : null;
        $this->query = 'SHOW TABLES'.$like;
        return $this;
    }
    
    /**
     * Query for truncating a table
     *
     * @param *string $table The name of the table
     *
     * @return QueryUtility
     */
    public function truncate($table)
    {
        $this->query = 'TRUNCATE `' . $table .'`';
        return $this;
    }
}
