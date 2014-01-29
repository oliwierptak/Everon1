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
    abstract protected function getFetchAllSql(array $criteria);
    
    public function __construct(Table $Table, Schema $Schema)
    {
        $this->SchemaTable = $Table;
        $this->Schema = $Schema;
    }
    
    public function add(Entity $Entity)
    {
        list($sql, $parameters) = $this->getInsertSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->execute($sql, $parameters);  //todo: fixD law of demeter violation
    }
    
    public function save(Entity $Entity)
    {
        list($sql, $parameters) = $this->getUpdateSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->execute($sql, $parameters);
    }

    /**
     * @param Entity $Entity
     * @return \PDOStatement
     */
    public function delete(Entity $Entity)
    {
        list($sql, $parameters) = $this->getDeleteSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->execute($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function fetchOne($id)
    {
        list($sql, $parameters) = $this->getFetchOneSql($id);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $parameters);
    }
    
    public function fetchAll(array $criteria)
    {
        list($sql, $parameters) = $this->getFetchAllSql($criteria);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $parameters);
    }

    public function getName()
    {
        return $this->getSchemaTable()->getName();
    }

    /**
     * @param Entity $Entity
     * @return array
     */
    public function getValuesForQuery(Entity $Entity)
    {
        $values = [];
        foreach ($this->getSchemaTable()->getColumns() as $column_name) {//eg. column_name=user_data, Entity->getUserData(), $Entity->data['user_data']
            $values[] = $Entity->getValueByName($column_name);
        }

        return $values;
    }

    public function getPlaceholderForQuery($delimeter=':')
    {
        $placeholders = [];
        foreach ($this->getSchemaTable()->getColumns() as $column_name) {
            $placeholders[] = $delimeter.$column_name;
        }

        return $placeholders;
    }
}