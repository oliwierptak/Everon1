<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\PostgreSql;

use Everon\DataMapper\Schema;
use Everon\DataMapper\Interfaces;

class Reader extends Schema\Reader implements Interfaces\Schema\Reader
{

    protected function getTablesSql()
    {
        return "
            SELECT *, table_name AS \"TABLE_NAME\"
            FROM information_schema.tables
            WHERE 
                table_type = 'BASE TABLE'
                AND table_schema NOT IN ('pg_catalog', 'information_schema')
                AND table_catalog = :schema
        ";
    }
    
    protected function getConstraintList()
    {
        return "
            SELECT
                tc.constraint_type, tc.constraint_name, tc.table_name, kcu.column_name,
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name,
                tc.table_name AS \"TABLE_NAME\"
            FROM 
                information_schema.table_constraints AS tc 
            JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            ORDER BY
                tc.table_name, tc.constraint_name
        ";
    }

    protected function getColumnsSql()
    {
        return "
            SELECT *, table_name AS \"TABLE_NAME\"
            FROM information_schema.columns
            WHERE 1=1
                AND table_schema NOT IN ('pg_catalog', 'information_schema')
                AND table_catalog = :schema
        ";
    }

    protected function getUniqueKeysSql()
    {
        return "
            SELECT *, tc.table_name AS \"TABLE_NAME\"
            FROM
                information_schema.table_constraints tc,  
                information_schema.key_column_usage kcu  
            WHERE
                tc.constraint_type = 'UNIQUE'
                AND kcu.table_name = kcu.table_name
                AND kcu.table_schema = tc.table_schema
                AND kcu.constraint_name = tc.constraint_name
                AND kcu.table_catalog = :schema
            ORDER BY tc.table_schema, tc.table_name
        ";
    }
    
    protected function getPrimaryKeysSql()
    {
        return "
            SELECT *, tc.table_name AS \"TABLE_NAME\"
            FROM
                information_schema.table_constraints tc,  
                information_schema.key_column_usage kcu  
            WHERE
                tc.constraint_type = 'PRIMARY KEY'
                AND kcu.table_name = kcu.table_name
                AND kcu.table_schema = tc.table_schema
                AND kcu.constraint_name = tc.constraint_name
                AND kcu.table_catalog = :schema
            ORDER BY tc.table_schema, tc.table_name
        ";
    }

    protected function getForeignKeysSql()
    {
        return "
            SELECT *, tc.table_name AS \"TABLE_NAME\"
            FROM
                information_schema.table_constraints tc,  
                information_schema.key_column_usage kcu  
            WHERE
                tc.constraint_type = 'FOREIGN KEY'
                AND kcu.table_name = kcu.table_name
                AND kcu.table_schema = tc.table_schema
                AND kcu.constraint_name = tc.constraint_name
                AND kcu.table_catalog = :schema
            ORDER BY tc.table_schema, tc.table_name
        ";
    }
}