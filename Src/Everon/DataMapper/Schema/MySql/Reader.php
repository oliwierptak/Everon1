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

use Everon\DataMapper\Interfaces;
use Everon\Interfaces\PdoAdapter;
use Everon\Domain\Interfaces\Entity;

class Reader implements Interfaces\SchemaMapper
{
    protected $name = null;

    /**
     * @var PdoAdapter
     */
    protected $Pdo = null;


    public function __construct($name, PdoAdapter $Pdo)
    {
        $this->name = $name;
        $this->Pdo = $Pdo;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getTableList()
    {
        $sql = "SHOW TABLES FROM :schema";
        return $this->Pdo->exec($sql, ['schema'=>$this->getName()], \PDO::FETCH_COLUMN);
    }

    public function getColumnList()
    {
        $sql = "
            SELECT
                * FROM information_schema.COLUMNS 
            WHERE  
                information_schema.COLUMNS.TABLE_SCHEMA = :schema
            ORDER BY
                information_schema.COLUMNS.ORDINAL_POSITION ASC
        ";

        return $this->Pdo->exec($sql, ['schema'=>$this->getName()], \PDO::FETCH_ASSOC);
    }

    public function getConstraintList()
    {
        $sql = "
            SELECT
                * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE  
                information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = :schema
        ";

        return $this->Pdo->exec($sql, ['schema'=>$this->getName()], \PDO::FETCH_ASSOC);
    }

    public function getForeignKeyList()
    {
        $sql = "
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

        return $this->Pdo->exec($sql, ['schema'=>$this->getName()], \PDO::FETCH_ASSOC);
    }

    public function getColumnsForTable($table_name)
    {
        $sql = "
            SELECT
                * FROM information_schema.COLUMNS 
            WHERE  
                information_schema.COLUMNS.TABLE_SCHEMA = :schema AND
                information_schema.COLUMNS.TABLE_NAME = :table
            ORDER BY
                information_schema.COLUMNS.ORDINAL_POSITION ASC
        ";        

        return $this->Pdo->exec($sql, ['schema'=>$this->getName(), 'table'=>$table_name], \PDO::FETCH_ASSOC);
    }

    public function getConstraintsForTable($table_name)
    {
        $sql = "
            SELECT 
                * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE  
                information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = :schema AND
                information_schema.TABLE_CONSTRAINTS.TABLE_NAME = :table
        ";
        
        return $this->Pdo->exec($sql, ['schema'=>$this->getName(), 'table'=>$table_name], \PDO::FETCH_ASSOC);
    }

    public function getForeignKeysForTable($table_name)
    {
        $sql = "
            SELECT
                TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
            FROM
                information_schema.KEY_COLUMN_USAGE
            WHERE 
                information_schema.KEY_COLUMN_USAGE.CONSTRAINT_SCHEMA = :schema AND
                information_schema.KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME IS NOT NULL AND
                information_schema.KEY_COLUMN_USAGE.TABLE_NAME = :table
        ";

        return $this->Pdo->exec($sql, ['schema'=>$this->getName(), 'table'=>$table_name], \PDO::FETCH_ASSOC);
    }
}