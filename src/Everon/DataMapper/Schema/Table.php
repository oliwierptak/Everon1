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

use Everon\DataMapper\Interfaces;
use Everon\DataMapper\Exception;
use Everon\Helper;

class Table implements Interfaces\Schema\Table
{
    use Helper\Asserts\IsNumericAndNonZero;
    use Helper\Exceptions;
    use Helper\Immutable;
    
    protected $name = null;
    
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
    public function getForeignKeys()
    {
        return $this->foreign_keys;
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
                'Invalid ID value: "%s" for column: "%s" in table: "%s"', $id, $Column->getName(), $this->getFullName()
            ), 'Everon\DataMapper\Exception\Table');
        }
        return $id;
    }

    /**
     * @inheritdoc
     */
    public function validateData(array $data, $validate_id)
    {
        $entity_data = [];
        /**
         * @var Interfaces\Schema\Column $Column
         */
        foreach ($this->getColumns() as $name => $Column) {
            if ($Column->isPk()) {
                $entity_data[$name] = null;
                continue;
            }

            $value = $Column->validateColumnValue($data[$name]);
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
    
    public function __toString()
    {
        return (string) $this->name;
    }
}
