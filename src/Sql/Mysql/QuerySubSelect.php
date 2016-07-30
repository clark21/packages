<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Sql\MySql;

use Cradle\Sql\QuerySelect;

/**
 * Generates subselect query string syntax
 *
 * @vendor   Cradle
 * @package  Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class QuerySubSelect
{
	/**
	 * @var SqlQuerySelect $parentQuery
	 */
    protected $parentQuery;
    
    /**
     * Construct: Set Parent Query and Column
     *
     * @param QuerySelect|null $parentQuery Main select query
     * @param string           $select      List of columns
     */
    public function __construct(QuerySelect $parentQuery = null, $select = '*')
    {
        $this->setParentQuery($parentQuery);
        $this->select = is_array($select) ? implode(', ', $select) : $select;
    }
    
    /**
     * Returns the string version of the query
     *
     * @return string
     */
    public function getQuery()
    {
        return '('.substr($this->parentQuery->getQuery(), 0, -1).')';
    }
    
    /**
     * Sets the parent Query
     *
     * @param $parentQuery Main select query
     *
     * @return QuerySubSelect
     */
    public function setParentQuery(QuerySelect $parentQuery)
    {
        $this->parentQuery = $parentQuery;
        return $this;
    }
}
