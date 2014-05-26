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

use Everon\Config;
use Everon\Dependency\Injection\Factory as FactoryDependency;
use Everon\DataMapper\Dependency;
use Everon\Domain\Dependency\DomainMapper as DomainMapperDependency;
use Everon\Domain;
use Everon\Helper;

class Schema implements Interfaces\Schema
{
    use Dependency\SchemaReader;
    use DomainMapperDependency;
    use FactoryDependency;
    
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
    public function __construct(Interfaces\Schema\Reader $SchemaReader, Interfaces\ConnectionManager $ConnectionManager, Domain\Interfaces\Mapper $DomainMapper)
    {
        $this->SchemaReader = $SchemaReader;
        $this->ConnectionManager = $ConnectionManager;
        $this->DomainMapper = $DomainMapper;
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
            $this->tables[$name] = $this->getFactory()->buildSchemaTableAndDependencies(
                $table_data['__TABLE_NAME_WITHOUT_SCHEMA'],
                $table_data['table_schema'],
                $this->getAdapterName(),
                $castToEmptyArrayWhenNull($name, $column_list), 
                $castToEmptyArrayWhenNull($name, $primary_key_list), 
                $castToEmptyArrayWhenNull($name, $unique_key_list), 
                $castToEmptyArrayWhenNull($name, $foreign_key_list),
                $this->getDomainMapper()
            );
        }
        
        $this->initViews();
    }
    
    protected function initViews()
    {
        /**
         * @var \Everon\Config\Interfaces\ItemDomain $Item
         */
        $mappings = $this->getDomainMapper()->toArray();
        foreach ($mappings as $domain_name => $Item) {
            if ($Item->getType() !== Config\Item\Domain::TYPE_MAT_VIEW) {
                continue;
            }
            
            $columns = $Item->getColumns();
            if (empty($columns)) {
                continue;
            }
            
            $view_columns = [];
            foreach ($columns as $view_column_name => $source) {
                $tokens = explode('.', $source);
                $column_name = array_pop($tokens);
                $item_table_name = implode('.', $tokens);
                
                if (isset($this->tables[$item_table_name]) === false) {
                    continue;
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\Table $Table
                 */
                $Table = $this->tables[$item_table_name];
                $Column = clone $Table->getColumnByName($column_name);
                $Column->setName($view_column_name);
                $view_columns[$view_column_name] = $Column;
            }

            $primary_keys = $Item->getPrimaryKeys();
            $view_primary_keys = [];
            foreach ($primary_keys as $view_key_name => $source) {
                $tokens = explode('.', $source);
                $column_name = array_pop($tokens);
                $item_table_name = implode('.', $tokens);

                if (isset($this->tables[$item_table_name]) === false) {
                    continue;
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\Table $Table
                 */
                $Table = $this->tables[$item_table_name];
                $view_primary_keys[$view_key_name] = $Table->getPrimaryKeyByName($column_name);
            }

            $tokens = explode('.', $Item->getTable());
            $table = array_pop($tokens);
            $schema_name = implode('.', $tokens);
            
            $this->tables[$Item->getTable()] = $this->getFactory()->buildSchemaTable(
                $table,
                $schema_name,
                $view_columns,
                $view_primary_keys,
                [],
                [],
                $this->getDomainMapper()
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