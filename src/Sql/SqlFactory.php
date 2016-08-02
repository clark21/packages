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

/**
 * Auto loads up the right handler given the PDO connection
 *
 * @package  Cradle
 * @category Sql
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class SqlFactory
{
    public static function load(PDO $connection)
    {
        $name = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        switch ($name) {
            case 'mysql':
                return MySQL::loadPDO($connection);
            case 'pgsql':
                return PostGreSql::loadPDO($connection);
            case 'sqlite':
                return Sqlite::loadPDO($connection);
            default:
                throw SqlException::forUnknownPDO($name);
        }
    }
}
