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
use Everon\Dependency\ConfigCacheLoader;
use Everon\Dependency\Injection\Factory as FactoryDependencyInjection;
use Everon\Dependency\Injection\Logger as LoggerDependencyInjection;
use Everon\DataMapper\Dependency;
use Everon\Dependency\Injection\ConfigManager as ConfigManagerDependencyInjection;
use Everon\Domain\Dependency\DomainMapper as DomainMapperDependency;
use Everon\Domain;
use Everon\FileSystem;
use Everon\Helper;

class Schema implements Interfaces\Schema
{
    use LoggerDependencyInjection;
    use FactoryDependencyInjection;
    use ConfigManagerDependencyInjection;
    use Dependency\SchemaReader;
    use DomainMapperDependency;
    
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
     * @var FileSystem\Interfaces\CacheLoader
     */
    protected $CacheLoader = null;

    /**
     * @var array
     */
    protected $pdo_adapters = null;

    /**
     * @var string
     */
    protected $database_timezone = null;

    /**
     * @param Interfaces\Schema\Reader $SchemaReader
     * @param Interfaces\ConnectionManager $ConnectionManager
     * @param Domain\Interfaces\Mapper $DomainMapper
     * @param FileSystem\Interfaces\CacheLoader $CacheLoader
     */
    public function __construct(
        Interfaces\Schema\Reader $SchemaReader, 
        Interfaces\ConnectionManager $ConnectionManager, 
        Domain\Interfaces\Mapper $DomainMapper,
        FileSystem\Interfaces\CacheLoader $CacheLoader
    )
    {
        $this->SchemaReader = $SchemaReader;
        $this->ConnectionManager = $ConnectionManager;
        $this->DomainMapper = $DomainMapper;
        $this->CacheLoader = $CacheLoader;
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
                $this->getDatabaseTimezone(),
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
                    throw new Exception\Schema('Invalid target table name: "%s"', $item_table_name);
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\Table $Table
                 * @var \Everon\DataMapper\Interfaces\Schema\Column $Column
                 */
                $Table = $this->tables[$item_table_name];
                $Column = clone $Table->getColumnByName($column_name);
                $Column->setName($view_column_name);
                $Column->unMarkAsPk(); //pk will be said based on primary keys
                $Column->setIsNullable(in_array($view_column_name, $Item->getNullable()));
                $view_columns[$view_column_name] = $Column;
            }

            $primary_keys = $Item->getPrimaryKeys();
            $view_primary_keys = [];
            foreach ($primary_keys as $view_key_name => $source) {
                $tokens = explode('.', $source);
                $column_name = array_pop($tokens);
                $item_table_name = implode('.', $tokens);

                if (isset($this->tables[$item_table_name]) === false) {
                    throw new Exception\Schema('Invalid target table name: "%s"', $item_table_name);
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\Table $Table
                 * @var \Everon\DataMapper\Interfaces\Schema\Column $Column
                 */
                $Column = $view_columns[$view_key_name];
                $Column->markAsPk();
                $Table = $this->tables[$item_table_name];
                $view_primary_keys[$view_key_name] = $Table->getPrimaryKeyByName($column_name);
            }

            $foreign_keys = $Item->getForeignKeys();
            $view_foreign_keys = [];
            foreach ($foreign_keys as $view_key_name => $source) {
                $tokens = explode('.', $source);
                $column_name = array_pop($tokens);
                $item_table_name = implode('.', $tokens);

                if (isset($this->tables[$item_table_name]) === false) {
                    throw new Exception\Schema('Invalid target table name: "%s"', $item_table_name);
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\Table $Table
                 * @var \Everon\DataMapper\Interfaces\Schema\Column $Column
                 */
                $Table = $this->tables[$Item->getTableOriginal()];
                $view_foreign_keys[$view_key_name] = $Table->getForeignKeyByTableName($item_table_name);
            }
            
            //todo unique keys are missing

            $tokens = explode('.', $Item->getTable());
            $table = array_pop($tokens);
            $schema_name = implode('.', $tokens);
            
            $SchemaView = $this->getFactory()->buildSchemaView(
                $Item->getTableOriginal(),
                $table,
                $schema_name,
                $view_columns,
                $view_primary_keys,
                [],
                $view_foreign_keys,
                $this->getDomainMapper()
            );

            $this->tables[$Item->getTable()] = $SchemaView;
        }
    }

    /**
     * @param FileSystem\Interfaces\CacheLoader $CacheLoader
     */
    public function setCacheLoader(FileSystem\Interfaces\CacheLoader $CacheLoader)
    {
        $this->CacheLoader = $CacheLoader;
    }

    /**
     * @return \Everon\FileSystem\Interfaces\CacheLoader
     */
    public function getCacheLoader()
    {
        return $this->CacheLoader;
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
    public function getTableByName($name)
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
        $name = ($name === 'read') ? 'write' : $name; //todo remove it, add support for transactions, add proxy method
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

    /**
     * @param string $database_locale
     */
    public function setDatabaseTimezone($database_locale)
    {
        $this->database_timezone = $database_locale;
    }

    /**
     * @return string
     */
    public function getDatabaseTimezone()
    {
        if ($this->database_timezone === null) {
            $this->database_timezone = $this->getConfigManager()->getConfigValue('application.locale.database_timezone', 'UTC');
        }
        return $this->database_timezone;
    }

    public function saveTablesToCache()
    {
        /**
         * @var Interfaces\Schema\Column $Column
         * @var Interfaces\Schema\Table $Table
         */

        $this->initTables();
        
        foreach ($this->tables as $table_name => $Table) {
            foreach ($Table->getColumns() as $column_name => $Column) {
                $Column->unsetFactory();
                $Column->getValidator()->unsetFactory();
            }
            
            $this->getCacheLoader()->saveToCache($table_name, $Table);
        }

    }
    
    public function loadTablesFromCache()
    {
        /**
         * @var Interfaces\Schema\Column $Column
         * @var Interfaces\Schema\Table $Table
         */

        $d = $this->getCacheLoader()->load();
        
        if (empty($d) === false) {
            $this->tables = &$d;
        }
        else {
            $this->initTables();
            return;
        }

        foreach ($this->tables as $table_name => $Table) {
            foreach ($Table->getColumns() as $column_name => $Column) {
                $Column->getValidator()->setFactory($this->getFactory());
                $Column->setFactory($this->getFactory());
            }
            //dd($Table);
            //$this->tables[$table_name] = $Table;
        }
    }

}