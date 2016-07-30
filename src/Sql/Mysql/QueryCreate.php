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
 * Generates create table query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryCreate extends AbstractQuery
{
    /**
     * @var string|null $name Name of table
     */
    protected $name = null;

    /**
     * @var string|null $comments Table comments
     */
    protected $comments = null;

    /**
     * @var array $fields List of fields
     */
    protected $fields = [];

    /**
     * @var array $keys List of key indexes
     */
    protected $keys = [];

    /**
     * @var array $uniqueKeys List of unique keys
     */
    protected $uniqueKeys = [];

    /**
     * @var array $primaryKeys List of primary keys
     */
    protected $primaryKeys = [];
    
    /**
     * Construct: Set the table, if any
     *
     * @param string|null $name Name of table
     */
    public function __construct($name = null)
    {
        if (is_string($name)) {
            $this->setName($name);
        }
    }
    
    /**
     * Adds a field in the table
     *
     * @param *string $name       Column name
     * @param *array  $attributes Column attributes
     *
     * @return Create
     */
    public function addField($name, array $attributes)
    {
        $this->fields[$name] = $attributes;
        return $this;
    }
    
    /**
     * Adds an index key
     *
     * @param *string $name   Name of key
     * @param *array  $fields List of key fields
     *
     * @return Create
     */
    public function addKey($name, array $fields)
    {
        $this->keys[$name] = $fields;
        return $this;
    }
    
    /**
     * Adds a primary key
     *
     * @param *string $name Name of key
     *
     * @return Create
     */
    public function addPrimaryKey($name)
    {
        $this->primaryKeys[] = $name;
        return $this;
    }
    
    /**
     * Adds a unique key
     *
     * @param *string $name   Name of key
     * @param *array  $fields List of key fields
     *
     * @return Create
     */
    public function addUniqueKey($name, array $fields)
    {
        $this->uniqueKeys[$name] = $fields;
        return $this;
    }
    
    /**
     * Returns the string version of the query
     *
     * @param bool $unbind Whether to unbind variables
     *
     * @return string
     */
    public function getQuery($unbind = false)
    {
        $table = '`'.$this->name.'`';
        
        $fields = [];
        foreach ($this->fields as $name => $attr) {
            $field = ['`'.$name.'`'];
            if (isset($attr['type'])) {
                $field[] = isset($attr['length']) && $attr['length'] ?
                    $attr['type'] . '('.$attr['length'].')' :
                    $attr['type'];
            }
            
            if (isset($attr['attribute'])) {
                $field[] = $attr['attribute'];
            }
            
            if (isset($attr['null'])) {
                if ($attr['null'] == false) {
                    $field[] = 'NOT NULL';
                } else {
                    $field[] = 'DEFAULT NULL';
                }
            }
            
            if (isset($attr['default'])&& $attr['default'] !== false) {
                if (!isset($attr['null']) || $attr['null'] == false) {
                    if (is_string($attr['default'])) {
                        $field[] = 'DEFAULT \''.$attr['default'] . '\'';
                    } else if (is_numeric($attr['default'])) {
                        $field[] = 'DEFAULT '.$attr['default'];
                    }
                }
            }
            
            if (isset($attr['auto_increment']) && $attr['auto_increment'] == true) {
                $field[] = 'auto_increment';
            }
            
            $fields[] = implode(' ', $field);
        }
        
        $fields = !empty($fields) ? implode(', ', $fields) : '';
        
        $primary = !empty($this->primaryKeys) ?
            ', PRIMARY KEY (`'.implode('`, `', $this->primaryKeys).'`)' :
            '';
        
        $uniques = [];
        foreach ($this->uniqueKeys as $key => $value) {
            $uniques[] = 'UNIQUE KEY `'. $key .'` (`'.implode('`, `', $value).'`)';
        }
        
        $uniques = !empty($uniques) ? ', ' . implode(", \n", $uniques) : '';
        
        $keys = [];
        foreach ($this->keys as $key => $value) {
            $keys[] = 'KEY `'. $key .'` (`'.implode('`, `', $value).'`)';
        }
        
        $keys = !empty($keys) ? ', ' . implode(", \n", $keys) : '';
        
        return sprintf(
            'CREATE TABLE %s (%s%s%s%s);',
            $table,
            $fields,
            $primary,
            $uniques,
            $keys
        );
    }
    
    /**
     * Sets comments
     *
     * @param *string $comments Table comments
     *
     * @return Create
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }
    
    /**
     * Sets a list of fields to the table
     *
     * @param *array $fields List of fields
     *
     * @return Create
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
    
    /**
     * Sets a list of keys to the table
     *
     * @param *array $keys List of keys
     *
     * @return Create
     */
    public function setKeys(array $keys)
    {
        $this->keys = $keys;
        return $this;
    }
    
    /**
     * Sets the name of the table you wish to create
     *
     * @param *string $name Table name
     *
     * @return Create
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Sets a list of primary keys to the table
     *
     * @param *array $primaryKeys List of primary keys
     *
     * @return Create
     */
    public function setPrimaryKeys(array $primaryKeys)
    {
        $this->primaryKeys = $primaryKeys;
        return $this;
    }
    
    /**
     * Sets a list of unique keys to the table
     *
     * @param *array $uniqueKeys List of unique keys
     *
     * @return Create
     */
    public function setUniqueKeys(array $uniqueKeys)
    {
        $this->uniqueKeys = $uniqueKeys;
        return $this;
    }
}
