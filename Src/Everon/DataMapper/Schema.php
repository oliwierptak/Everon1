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
    use Dependency\DataMapper\SchemaReader;
    use Dependency\Injection\Factory;
    use Helper\ToArray;

    
    protected $tables = [];
    
    protected $columns = [];
    
    protected $constraints = [];
    
    protected $foreign_keys = [];

    /**
     * @var Interfaces\ConnectionManager
     */
    protected $ConnectionManager = null;

    protected $pdo_adapters = null;


    /**
     * @param Interfaces\Schema\Reader $SchemaReader
     * @param Interfaces\ConnectionManager $ConnectionManager
     */
    public function __construct(Interfaces\Schema\Reader $SchemaReader, Interfaces\ConnectionManager $ConnectionManager)
    {
        $this->SchemaReader = $SchemaReader;
        $this->ConnectionManager = $ConnectionManager;

        $this->init();
    }

    protected function init()
    {
        $table_list = $this->getSchemaReader()->getTableList();
        $column_list = $this->getSchemaReader()->getColumnList();
        $constraint_list = $this->getSchemaReader()->getConstraintList();
        $foreign_key_list = $this->getSchemaReader()->getForeignKeyList();

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
            $this->columns[$name] = $filterPerTableName($name, $column_list);
            $this->constraints[$name] = $filterPerTableName($name, $constraint_list);
            $this->foreign_keys[$name] = $filterPerTableName($name, $foreign_key_list);
            
            $this->tables[$name] = $this->getFactory()->buildSchemaTable($name, $this->columns[$name], $this->constraints[$name], $this->foreign_keys[$name]);
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
        return $this->getSchemaReader()->getName();
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
            $PdoAdapter = $this->getFactory()->buildPdoAdapter($Pdo, $Connection);
            $this->pdo_adapters[$name] = $PdoAdapter;
        }

        return $this->pdo_adapters[$name];
    }
}