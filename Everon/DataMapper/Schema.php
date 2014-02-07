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
        $table_list = $this->getSchemaReader()->getTableList();
        $column_list = $this->getSchemaReader()->getColumnList();
        $primary_key_list = $this->getSchemaReader()->getPrimaryKeysList();
        $foreign_key_list = $this->getSchemaReader()->getForeignKeyList();
        $unique_key_list = $this->getSchemaReader()->getUniqueKeysList();

        $castToEmptyArrayWhenNull = function($name, $item) {
            return isset($item[$name]) ? $item[$name] : [];
        };
        
        foreach ($table_list as $name => $table_data) {
            $name = mb_strtolower($name);
            $this->tables[$name] = $this->getFactory()->buildSchemaTable(
                $name,
                @$table_data['table_schema'],
                $this->getAdapterName(),
                $castToEmptyArrayWhenNull($name, $column_list), 
                $castToEmptyArrayWhenNull($name, $primary_key_list), 
                $castToEmptyArrayWhenNull($name, $unique_key_list), 
                $castToEmptyArrayWhenNull($name, $foreign_key_list)
            );
        }
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
        if ($this->tables === null) {
            $this->initTables();
        }
        return $this->tables;
    }
    
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @param $name
     * @return Interfaces\Schema\Table
     */
    public function getTable($name)
    {
        $name = mb_strtolower($name);
        if ($this->tables === null) {
            $this->initTables();
        }
        
        return $this->tables[$name];
    }

    /**
     * @inheritdoc
     */
    public function getPdoAdapterByName($name)
    {
        $name = mb_strtolower($name);
        if (isset($this->pdo_adapters[$name]) === false) {
            $Connection = $this->getConnectionManager()->getConnectionByName($name);
            list($dsn, $username, $password, $options) = $Connection->toPdo();
            $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
            $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
            $this->pdo_adapters[$name] = $PdoAdapter;
        }

        return $this->pdo_adapters[$name];
    }
}