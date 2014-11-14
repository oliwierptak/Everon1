<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Dependency;
use Everon\DataMapper\Interfaces;
use Everon\DataMapper\Exception;
use Everon\Helper;

class Table implements Interfaces\Schema\Table
{
    use Dependency\Injection\Logger;
    
    use Helper\Asserts\IsNumericAndNotZero;
    use Helper\Asserts\IsStringAndNotEmpty;
    use Helper\Exceptions;
    use Helper\Immutable;
    
    protected $name = null;
    
    protected $original_name = null; //todo view_only
    
    protected $schema = null;
    
    protected $pk = null;
    
    protected $columns = [];
    
    protected $primary_keys = [];

    protected $foreign_keys = [];
    
    protected $unique_keys = [];
    

    /**
     * @param $name
     * @param $schema
     * @param array $columns
     * @param array $primary_keys
     * @param array $unique_keys
     * @param array $foreign_keys
     */
    public function __construct($name, $schema, array $columns, array $primary_keys,  array $unique_keys, array $foreign_keys)
    {        
        $this->name = $name;
        $this->schema = $schema;
        $this->columns = $columns;
        $this->primary_keys = $primary_keys;
        $this->foreign_keys = $foreign_keys;
        $this->unique_keys = $unique_keys;
        
        $this->init();
        $this->lock();
    }
    
    protected function init()
    {
        /**
         * @var Interfaces\Schema\Column $Column
         */
        foreach ($this->columns as $Column) {
            if ($Column->isPk()) {
                $this->pk = $Column->getName();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function getFullName()
    {
        return $this->schema.'.'.$this->name;
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @inheritdoc
     */
    public function getColumnByName($name)
    {
        if (isset($this->columns[$name]) === false) {
            throw new Exception\Table('Invalid column name: "%s" in table: "%s"', [$name, $this->getFullName()]);
        }
        return $this->columns[$name];
    }

    /**
     * @inheritdoc
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * @inheritdoc
     */
    public function getForeignKeys()
    {
        return $this->foreign_keys;
    }

    /**
     * @inheritdoc
     */
    public function getForeignKeyByName($name)
    {
        if (isset($this->foreign_keys[$name]) === false) {
            throw new Exception\Table('Invalid foreign key name: "%s" in table: "%s"', [$name, $this->getFullName()]);
        }
        return $this->foreign_keys[$name];
    }

    /**
     * @inheritdoc
     */
    public function getForeignKeyByTableName($foreign_table_name)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey 
         */
        foreach ($this->foreign_keys as $column_name => $ForeignKey) {
            if (strcasecmp($ForeignKey->getForeignFullTableName(), $foreign_table_name) === 0) {
                return $ForeignKey;
            }
        }
        
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeys()
    {
        return $this->primary_keys;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyByName($name)
    {
        if (isset($this->primary_keys[$name]) === false) {
            throw new Exception\Table('Invalid primary key name: "%s" in table: "%s"', [$name, $this->getFullName()]);
        }
        return $this->primary_keys[$name];
    }

    /**
     * @inheritdoc
     */
    public function getUniqueKeys()
    {
        return $this->unique_keys;
    }

    /**
     * @inheritdoc
     */
    public function toArray($deep=false)
    {
        return get_object_vars($this);
    }

    /**
     * @inheritdoc
     */
    public function getPk()
    {
        if (is_array($this->pk)) {
            return $this->pk[0];
        }
        return $this->pk;
    }

    /**
     * @inheritdoc
     */
    public function validateId($id)
    {
        $PrimaryKey = current($this->getPrimaryKeys()); //todo: make fix for composite keys
        $Column = $this->getColumnByName($PrimaryKey->getName());
        $id = $Column->validateColumnValue($id);
        
        if (is_integer($id)) {
            $this->assertIsNumericAndNonZero($id, sprintf(
                'Invalid numeric ID value: "%s" for column: "%s" in table: "%s"', $id, $Column->getName(), $this->getFullName()
            ), 'Everon\DataMapper\Exception\Table');
        }

        if (is_string($id)) {
            $this->assertIsStringAndNonEmpty($id, sprintf(
                'Invalid string ID value: "%s" for column: "%s" in table: "%s"', $id, $Column->getName(), $this->getFullName()
            ), 'Everon\DataMapper\Exception\Table');
        }
        
        return $id;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataForSql(array $data, $validate_id)
    {
        $entity_data = [];
        /**
         * @var Interfaces\Schema\Column $Column
         */
        foreach ($this->getColumns() as $name => $Column) {
            if ($Column->isPk()) {
                $entity_data[$Column->getName()] = null;
                continue;
            }
            
            if ($Column->isNullable() === false && array_key_exists($name, $data) === false) {
                throw new Exception\Table('Missing data value for column: "%s@%s"', [$this->getName(), $name]);
            }

            if (array_key_exists($name, $data) === false) {
                $this->getLogger()->warning('Entity property not set. Using null for: "%s@%s"', [$Column->getTable(), $name]);
                $data[$name] = null;
            }
            
            $value = $Column->getDataForSql($data[$name]);
            $value = $Column->validateColumnValue($value); //order of execution matters: getDataForSql() before validateColumnValue()
            $entity_data[$name] = $value;
        }

        if ($validate_id) {
            $pk_name = $this->getPk();
            $id = $this->getIdFromData($data);
            $id = $this->validateId($id);
            $entity_data[$pk_name] = $id;
        }

        return $entity_data;
    }
    
    /**
     * @inheritdoc
     */
    public function getIdFromData($data)
    {
        $pk_name = $this->getPk();
        if (isset($data[$pk_name]) === false) {
            return null;
        }

        return $data[$pk_name];
    }

    /**
     * @param string $original_name
     */
    public function setOriginalName($original_name)
    {
        $this->original_name = $original_name;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }
    
    public function __toString()
    {
        return (string) $this->name;
    }

    public function __sleep()
    {
        s('sleep');
        return [
            'name',
        ];
    }

    public function __wakeup()
    {
        s('wakeup');
    }

    public static function __set_state(array $parameters)
    {
        s('set_state', $parameters);
    }
}
