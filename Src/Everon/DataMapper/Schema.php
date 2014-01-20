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

use Everon\Dependency;
use Everon\Helper;

class Schema implements Interfaces\Schema
{
    use Dependency\Injection\Factory;
    use Helper\ToArray;
    
    protected $name = null;
    
    protected $tables = array();

    /**
     * @var Interfaces\ConnectionManager
     */
    protected $ConnectionManager = null;

    /**
     * @var Interfaces\SchemaMapper
     */
    protected $SchemaMapper = null;
    
    protected $pdo_adapters = null;

    
    public function __construct($name, Interfaces\ConnectionManager $ConnectionManager, Interfaces\SchemaMapper $SchemaMapper)
    {
        $this->name = $name;
        $this->ConnectionManager = $ConnectionManager;
        $this->SchemaMapper = $SchemaMapper;

        $this->init();
    }
    
/*    protected function initPdo()
    {
        $Connection = $this->getConnectionManager()->getConnectionByName('schema');
        list($dsn, $username, $password, $options) = $Connection->toPdo();
        $this->SchemaMapper = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
    }*/
    
    protected function init()
    {
        $table_list = $this->SchemaMapper->getTableList();
        $column_list = $this->SchemaMapper->getColumnList();
        $constraint_list = $this->SchemaMapper->getConstraintList();
        $foreign_key_list = $this->SchemaMapper->getForeignKeyList();

        $filterPerTableName = function($table_name, $data) {
            $result = [];
            foreach ($data as $item) {
                if ($item['TABLE_NAME'] === $table_name) {
                    $result[] = $item;
                }
            }
            return $result;
        };

        foreach ($table_list as $name) {
            $columns = $filterPerTableName($name, $column_list);
            $constraints = $filterPerTableName($name, $constraint_list);
            $foreign_keys = $filterPerTableName($name, $foreign_key_list);
            
            $this->tables[$name] = $this->getFactory()->buildSchemaTable($name, $columns, $constraints, $foreign_keys);
        }
    }
    
    /**
     * @return Interfaces\ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->ConnectionManager;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getTables()
    {
        return $this->tables;
    }
    
    public function setTables($tables)
    {
        $this->tables = $tables;
    }
    
    public function getTable($name)
    {
        return $this->tables[$name];
    }

    /**
     * @inheritdoc
     */
    public function getPdoAdapter($name)
    {
        if (isset($this->pdo_adapters[$name]) === false) {
            $Connection = $this->getConnectionManager()->getConnectionByName($name);
            list($dsn, $username, $password, $options) = $Connection->toPdo();
            $Pdo = $this->getFactory()->buildPdo($dsn, $username, $password, $options);
            $this->pdo_adapters[$name] = $Pdo;
        }

        return $this->pdo_adapters[$name];
    }
}