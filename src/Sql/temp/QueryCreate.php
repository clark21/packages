<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql\PostGreSql;

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
     * @var array $fields List of fields
     */
    protected $fields = [];

    /**
     * @var array $primaryKeys List of primary keys
     */
    protected $primaryKeys = [];

    /**
     * @var array $oids Whether to use OIDs
     */
    protected $oids = false;
    
    /**
     * Construct: set table name, if given
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
     * @return QueryCreate
     */
    public function addField($name, array $attributes)
    {
        $this->fields[$name] = $attributes;
        return $this;
    }
    
    /**
     * Adds a primary key
     *
     * @param *string $name Name of key
     *
     * @return QueryCreate
     */
    public function addPrimaryKey($name)
    {
        $this->primaryKeys[] = $name;
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
        $table = '"'.$this->name.'"';
        
        $fields = [];
        foreach ($this->fields as $name => $attr) {
            $field = ['"'.$name.'"'];
            if (isset($attr['type'])) {
                $field[] = isset($attr['length']) ?
                    $attr['type'] . '('.$attr['length'].')' :
                    $attr['type'];
                
                if (isset($attr['list']) && $attr['list']) {
                    $field[count($field)-1].='[]';
                }
            }
            
            if (isset($attr['attribute'])) {
                $field[] = $attr['attribute'];
            }
            
            if (isset($attr['unique']) && $attr['unique']) {
                $field[] = 'UNIQUE';
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
            
            $fields[] = implode(' ', $field);
        }
        
        $oids = $this->oids ? 'WITH OIDS': null;
        $fields = !empty($fields) ? implode(', ', $fields) : '';
        $primary = !empty($this->primaryKeys) ?
            ', PRIMARY KEY ("'.implode('", ""', $this->primaryKeys).'")' :
            '';
        
        return sprintf('CREATE TABLE %s (%s%s) %s;', $table, $fields, $primary, $oids);
    }
    
    /**
     * Sets a list of fields to the table
     *
     * @param array $fields List of fields
     *
     * @return QueryCreate
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
    
    /**
     * Sets the name of the table you wish to create
     *
     * @param *string $name Table name
     *
     * @return QueryCreate
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
     * @return QueryCreate
     */
    public function setPrimaryKeys(array $primaryKeys)
    {
        $this->primaryKeys = $primaryKeys;
        return $this;
    }
    
    /**
     * Specifying if query should add the OIDs as columns
     *
     * @param bool $oids true or false
     *
     * @return QueryCreate
     */
    public function withOids($oids)
    {
        $this->oids = $oids;
        return $this;
    }
}
