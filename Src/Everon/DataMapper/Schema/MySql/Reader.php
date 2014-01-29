<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\MySql;

use Everon\DataMapper\Schema;
use Everon\DataMapper\Interfaces;

class Reader extends Schema\Reader implements Interfaces\Schema\Reader
{
    protected function getTablesSql()
    {
        return "SELECT * FROM information_schema.TABLES WHERE table_schema = :schema";
    }

    protected function getColumnsSql()
    {
        return "
            SELECT
                * FROM information_schema.COLUMNS 
            WHERE  
                information_schema.COLUMNS.TABLE_SCHEMA = :schema
            ORDER BY
                information_schema.COLUMNS.ORDINAL_POSITION ASC
        ";
    }

    protected function getConstraintsSql()
    {
        return "
            SELECT
                * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE  
                information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = :schema
        ";
    }

    protected function getForeignKeysSql()
    {
        return "
            SELECT
                TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
            FROM
                information_schema.KEY_COLUMN_USAGE
            WHERE 
                information_schema.KEY_COLUMN_USAGE.CONSTRAINT_SCHEMA = :schema AND
                information_schema.KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY 
                information_schema.KEY_COLUMN_USAGE.TABLE_NAME
        ";
    }
}