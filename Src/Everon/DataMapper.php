<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\DataMapper\Interfaces\Schema;
use Everon\DataMapper\Interfaces\Criteria;
use Everon\DataMapper\Interfaces\Schema\Table;
use Everon\DataMapper\Dependency;
use Everon\Domain\Interfaces\Entity;
use Everon\Interfaces;

abstract class DataMapper implements Interfaces\DataMapper
{
    use Dependency\Schema;
    use Dependency\SchemaTable;
    
    protected $write_connection_name = 'write';
    protected $read_connection_name = 'read';
    
    abstract protected function getInsertSql(Entity $Entity);
    abstract protected function getUpdateSql(Entity $Entity);
    abstract protected function getDeleteSql(Entity $Entity);
    abstract protected function getFetchOneSql($id);
    abstract protected function getFetchAllSql(Criteria $Criteria);
    
    public function __construct(Table $Table, Schema $Schema)
    {
        $this->SchemaTable = $Table;
        $this->Schema = $Schema;
    }
    
    public function add(Entity $Entity)
    {
        list($sql, $parameters) = $this->getInsertSql($Entity);
        $PdoAdapter = $this->getSchema()->getPdoAdapterByName($this->write_connection_name);
        $id = $PdoAdapter->insert($sql, $parameters);
        return $this->getSchemaTable()->validateId($id);
    }
    
    public function save(Entity $Entity)
    {
        $id = $Entity->getId();
        $id = $this->getSchemaTable()->validateId($id);
        list($sql, $parameters) = $this->getUpdateSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->update($sql, $parameters);
    }

    /**
     * @param Entity $Entity
     * @return \PDOStatement
     */
    public function delete(Entity $Entity)
    {
        $id = $Entity->getId();
        $id = $this->getSchemaTable()->validateId($id);
        list($sql, $parameters) = $this->getDeleteSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->delete($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function fetchOne($id)
    {
        $id = $this->getSchemaTable()->validateId($id);
        list($sql, $parameters) = $this->getFetchOneSql($id);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $parameters)->fetch();
    }
    
    public function fetchAll(Criteria $Criteria)
    {
        list($sql, $parameters) = $this->getFetchAllSql($Criteria);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $parameters)->fetchAll();
    }

    public function getName()
    {
        return $this->getSchemaTable()->getName();
    }

    /**
     * @param string $placeholder
     * @return array
     */
    public function getPlaceholderForQuery($placeholder=':')
    {
        $placeholders = [];
        $columns = $this->getSchemaTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            $placeholders[] = $placeholder.$name;
        }

        return $placeholders;
    }

    /**
     * @param Entity $Entity
     * @param string $delimiter
     * @return array
     */
    public function getValuesForQuery(Entity $Entity, $delimiter='')
    {
        $values = [];
        $columns = $this->getSchemaTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            $values[$delimiter.$name] = $this->getEntityValueAndRemapId($name, $Entity);
        }

        return $values;
    }

    protected function getEntityValueAndRemapId($value_name, Entity $Entity)
    {
        if (strcasecmp($value_name, $this->getSchemaTable()->getPk()) === 0) {
            $value = $Entity->getId();
        }
        else {
            $value = $Entity->getValueByName($value_name);    
        }
        return $value;
    }
}