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

use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Interfaces\Schema\Columnq;
use Everon\DataMapper\Exception;
use Everon\Domain\Interfaces\Entity;
use Everon\Helper;

class Table implements Schema\Table
{
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
         * @var Schema\Column $Column
         */
        foreach ($this->columns as $Column) {
            if ($Column->isPk()) {
                $this->pk = $Column->getName();
            }
        }
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSchema()
    {
        return $this->schema;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getPrimaryKeys()
    {
        return $this->primary_keys;
    }
    
    public function getUniqueKeys()
    {
        return $this->unique_keys;
    }
    
    public function toArray()
    {
        return get_object_vars($this);
    }
    
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
        /**
         * @var Column $Column
         */
        $Column = $this->getColumns()[$PrimaryKey->getName()];
        $validation_result = filter_var_array([$PrimaryKey->getName() => $id], $Column->getValidationRules());
        if (($validation_result === false || $validation_result === null) || 
            ($Column->isNullable() === false && $id === null)) {
            throw new Exception\Column('Column: "%s" failed to validate with value: "%s"',[$Column->getName(), $id]);
        }

        return $validation_result[$PrimaryKey->getName()];
    }
    
    public function __toString()
    {
        return (string) $this->name;
    }
}
