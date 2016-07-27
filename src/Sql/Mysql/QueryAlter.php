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
 * Generates alter query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QueryAlter extends AbstractQuery
{
    /**
     * @var string|null $name Name of table
     */
    protected $name = null;

    /**
     * @var array $changeFields List of fields to change
     */
    protected $changeFields = [];

    /**
     * @var array $addFields List of fields to add
     */
    protected $addFields = [];

    /**
     * @var array $removeFields List of fields to remove
     */
    protected $removeFields = [];

    /**
     * @var array $addKeys List of keys to add
     */
    protected $addKeys = [];

    /**
     * @var array $removeKeys List of keys to remove
     */
    protected $removeKeys = [];

    /**
     * @var array $addUniqueKeys List of unique keys to add
     */
    protected $addUniqueKeys = [];

    /**
     * @var array $removeUniqueKeys List of unique keys to remove
     */
    protected $removeUniqueKeys = [];

    /**
     * @var array $addPrimaryKeys List of primary keys to add
     */
    protected $addPrimaryKeys = [];

    /**
     * @var array $removePrimaryKeys List of primary keys to remove
     */
    protected $removePrimaryKeys = [];
    
    /**
     * Construct: Set the table, if any
     *
     * @param string|null $name Table name
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
     * @return QueryAlter
     */
    public function addField($name, array $attributes)
    {
        $this->addFields[$name] = $attributes;
        return $this;
    }
    
    /**
     * Adds an index key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function addKey($name)
    {
        $this->addKeys[] = '`'.$name.'`';
        return $this;
    }
    
    /**
     * Adds a primary key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function addPrimaryKey($name)
    {
        $this->addPrimaryKeys[] = '`'.$name.'`';
        return $this;
    }
    
    /**
     * Adds a unique key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function addUniqueKey($name)
    {
        $this->addUniqueKeys[] = '`'.$name.'`';
        return $this;
    }
    
    /**
     * Changes attributes of the table given
     * the field name
     *
     * @param *string $name       Column name
     * @param *array  $attributes Column attributes
     *
     * @return QueryAlter
     */
    public function changeField($name, array $attributes)
    {
        $this->changeFields[$name] = $attributes;
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
        $fields = [];
        $table = '`'.$this->name.'`';
        
        foreach ($this->removeFields as $name) {
            $fields[] = 'DROP `'.$name.'`';
        }
        
        foreach ($this->addFields as $name => $attr) {
            $field = ['ADD `'.$name.'`'];
            if (isset($attr['type'])) {
                $field[] = isset($attr['length']) ?
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
        
        foreach ($this->changeFields as $name => $attr) {
            $field = ['CHANGE `'.$name.'`  `'.$name.'`'];
            
            if (isset($attr['name'])) {
                $field = ['CHANGE `'.$name.'`  `'.$attr['name'].'`'];
            }
            
            if (isset($attr['type'])) {
                $field[] = isset($attr['length']) ? $attr['type'] . '('.$attr['length'].')' : $attr['type'];
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
        
        foreach ($this->removeKeys as $key) {
            $fields[] = 'DROP INDEX `'.$key.'`';
        }
        
        if (!empty($this->addKeys)) {
            $fields[] = 'ADD INDEX ('.implode(', ', $this->addKeys).')';
        }
        
        foreach ($this->removeUniqueKeys as $key) {
            $fields[] = 'DROP INDEX `'.$key.'`';
        }
        
        if (!empty($this->addUniqueKeys)) {
            $fields[] = 'ADD UNIQUE ('.implode(', ', $this->addUniqueKeys).')';
        }
        
        foreach ($this->removePrimaryKeys as $key) {
            $fields[] = 'DROP PRIMARY KEY `'.$key.'`';
        }
        
        if (!empty($this->addPrimaryKeys)) {
            $fields[] = 'ADD PRIMARY KEY ('.implode(', ', $this->addPrimaryKeys).')';
        }
        
        $fields = implode(", \n", $fields);
        
        return sprintf(
            'ALTER TABLE %s %s;',
            $table,
            $fields
        );
    }
    
    /**
     * Removes a field
     *
     * @param *string $name Column name
     *
     * @return QueryAlter
     */
    public function removeField($name)
    {
        $this->removeFields[] = $name;
        return $this;
    }
    
    /**
     * Removes an index key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function removeKey($name)
    {
        $this->removeKeys[] = $name;
        return $this;
    }
    
    /**
     * Removes a primary key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function removePrimaryKey($name)
    {
        $this->removePrimaryKeys[] = $name;
        return $this;
    }
    
    /**
     * Removes a unique key
     *
     * @param *string $name Name of key
     *
     * @return QueryAlter
     */
    public function removeUniqueKey($name)
    {
        $this->removeUniqueKeys[] = $name;
        return $this;
    }
    
    /**
     * Sets the name of the table you wish to create
     *
     * @param *string $name Table name
     *
     * @return QueryAlter
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
