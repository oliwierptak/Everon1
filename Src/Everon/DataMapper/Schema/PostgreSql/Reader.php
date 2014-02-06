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
                -- table_schema <> 'information_schema' AND table_schema !~ E'^pg_'
                AND table_schema NOT IN ('pg_catalog', 'information_schema')
                AND table_catalog = :schema
        ";
    }

    protected function getColumnsSql()
    {
        return "
            SELECT *, tab_columns.table_name AS \"TABLE_NAME\", tab_columns.column_name AS \"column_name\" 
            FROM information_schema.columns AS tab_columns
            LEFT OUTER JOIN
                information_schema.constraint_column_usage AS col_constraints
                ON tab_columns.table_name = col_constraints.table_name AND
                tab_columns.column_name = col_constraints.column_name
            LEFT OUTER JOIN
                information_schema.table_constraints AS tab_constraints
                ON tab_constraints.constraint_name = col_constraints.constraint_name
            LEFT OUTER JOIN
                information_schema.check_constraints AS col_check_constraints
                ON col_check_constraints.constraint_name = tab_constraints.constraint_name
            WHERE 1=1
                AND tab_columns.table_schema NOT IN ('pg_catalog', 'information_schema')
                AND tab_columns.table_catalog = :schema
            ORDER BY
                 tab_columns.table_name, ordinal_position
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