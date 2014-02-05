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
                table_schema <> 'information_schema' AND table_schema !~ E'^pg_'
                AND table_catalog = :schema
        ";
    }

    protected function getColumnsSql()
    {
        return "
            SELECT *, table_name AS \"TABLE_NAME\"
            FROM information_schema.columns
            WHERE
                table_schema <> 'information_schema' AND table_schema !~ E'^pg_'
                AND table_catalog = :schema
            ORDER BY
                table_schema, table_name, column_name
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