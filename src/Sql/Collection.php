<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql;

use Cradle\Data\Model as DataModel;
use Cradle\Data\Collection as DataCollection;

/**
 * Sql Collection handler
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Collection extends DataCollection
{
    /**
     * @var SqlInterface|null $database The database resource
     */
    protected $database = null;
       
    /**
     * @var string|null $table Default table name
     */
    protected $table = null;
    
    /**
     * Returns the entire data
     *
     * @param array $row
     *
     * @return Model
     */
    public function getModel(array $row = [])
    {
        $model = $this->resolve(Model::class, $row);
        
        if (!is_null($this->database)) {
            $model->setDatabase($this->database);
        }
        
        if (!is_null($this->table)) {
            $model->setTable($this->table);
        }
        
        return $model;
    }
    
    /**
     * Sets the default database
     *
     * @param SqlInterface $database Database object instance
     *
     * @return Collection
     */
    public function setDatabase(SqlInterface $database)
    {
        $this->database = $database;
        
        //for each row
        foreach ($this->data as $row) {
            if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
                continue;
            }
            
            //let the row handle this
            $row->setDatabase($database);
        }
        
        return $this;
    }
    
    /**
     * Sets the default database
     *
     * @param string $table The name of the table
     *
     * @return Collection
     */
    public function setTable($table)
    {
        $this->table = $table;
        
        //for each row
        foreach ($this->data as $row) {
            if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
                continue;
            }
            
            //let the row handle this
            $row->setTable($table);
        }
        
        return $this;
    }
}
