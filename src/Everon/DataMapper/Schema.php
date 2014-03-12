<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper;

use Everon\Dependency\Injection\Factory as FactoryInjection;
use Everon\DataMapper\Dependency;
use Everon\Helper;

class Schema implements Interfaces\Schema
{
    use Dependency\SchemaReader;
    use FactoryInjection;
    use Helper\ToArray;

    /**
     * @var array
     */
    protected $tables = null;

    /**
     * @var Interfaces\ConnectionManager
     */
    protected $ConnectionManager = null;

    /**
     * @var array
     */
    protected $pdo_adapters = null;


    /**
     * @param Interfaces\Schema\Reader $SchemaReader
     * @param Interfaces\ConnectionManager $ConnectionManager
     */
    public function __construct(Interfaces\Schema\Reader $SchemaReader, Interfaces\ConnectionManager $ConnectionManager)
    {
        $this->SchemaReader = $SchemaReader;
        $this->ConnectionManager = $ConnectionManager;
    }
    
    protected function initTables()
    {
        if ($this->tables !== null) {
            return;
        }
        
        $table_list = $this->getSchemaReader()->getTableList();
        $column_list = $this->getSchemaReader()->getColumnList();
        $primary_key_list = $this->getSchemaReader()->getPrimaryKeysList();
        $foreign_key_list = $this->getSchemaReader()->getForeignKeyList();
        $unique_key_list = $this->getSchemaReader()->getUniqueKeysList();

        $castToEmptyArrayWhenNull = function($name, $item) {
            return isset($item[$name]) ? $item[$name] : [];
        };
        
        foreach ($table_list as $name => $table_data) {
            $table_schema_name = trim($table_data['table_schema']);
            $table_id = strtolower($table_schema_name ? $table_data['table_schema'].'.'.$name : $name); 
            $this->tables[$table_id] = $this->getFactory()->buildSchemaTable(
                $name,
                $table_data['table_schema'],
                $this->getAdapterName(),
                $castToEmptyArrayWhenNull($name, $column_list), 
                $castToEmptyArrayWhenNull($name, $primary_key_list), 
                $castToEmptyArrayWhenNull($name, $unique_key_list), 
                $castToEmptyArrayWhenNull($name, $foreign_key_list)
            );
        }
        
        $this->tables = array_change_key_case($this->tables, \CASE_LOWER);
    }
    
    /**
     * @return Interfaces\ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->ConnectionManager;
    }

    /**
     * @inheritdoc
     */
    public function getDriver()
    {
        return $this->getSchemaReader()->getDriver();
    }
    
    public function getDatabase()
    {
        return $this->getSchemaReader()->getDatabase();
    }
    
    public function getAdapterName()
    {
        return $this->getSchemaReader()->getAdapterName();
    }
    
    public function getTables()
    {
        $this->initTables();
        return $this->tables;
    }
    
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @inheritdoc
     */
    public function getTable($name)
    {
        $this->initTables();
        $name = mb_strtolower($name);
        
        if (isset($this->tables[$name]) === false) {
            throw new Exception\Schema('Invalid schema table name: "%s"', $name);
        }
        return $this->tables[$name];
    }

    /**
     * @inheritdoc
     */
    public function getPdoAdapterByName($name)
    {
        if (isset($this->pdo_adapters[$name]) === false) {
            $Connection = $this->getConnectionManager()->getConnectionByName($name);
            list($dsn, $username, $password, $options) = $Connection->toPdo();
            $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
            $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
            $this->pdo_adapters[$name] = $PdoAdapter;
        }

        if (isset($this->pdo_adapters[$name]) === false) {
            throw new Exception\Schema('Invalid PdoAdapter name: "%s"', $name);
        }

        return $this->pdo_adapters[$name];
    }
}