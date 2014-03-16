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

    /**
     * @var Table
     */
    protected $Table = null;
    
    protected $write_connection_name = 'write';
    protected $read_connection_name = 'read';
    
    abstract protected function getInsertSql(Entity $Entity);
    abstract protected function getUpdateSql(Entity $Entity);
    abstract protected function getDeleteSql(Entity $Entity);
    abstract protected function getFetchAllSql(Criteria $Criteria);


    /**
     * @param Table $Table
     * @param Schema $Schema
     */
    public function __construct(Table $Table, Schema $Schema)
    {
        $this->Table = $Table;
        $this->Schema = $Schema;
    }

    /**
     * @param string $placeholder
     * @return array
     */
    protected function getPlaceholderForQuery($placeholder=':', $skip_pk=false)
    {
        $placeholders = [];
        $columns = $this->getTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            if ($skip_pk === true && $Column->isPk()) {
                continue;
            }
            $placeholders[] = $placeholder.$name;
        }

        return $placeholders;
    }

    /**
     * @param Entity $Entity
     * @param string $delimiter
     * @return array
     */
    protected function getValuesForQuery(Entity $Entity, $delimiter='', $skip_pk=false)
    {
        $values = [];
        $columns = $this->getTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            if ($skip_pk === true && $Column->isPk()) {
                continue;
            }
            $values[$delimiter.$name] = $this->getEntityValueAndRemapId($name, $Entity);
        }

        return $values;
    }

    /**
     * @param $value_name
     * @param Entity $Entity
     * @return mixed
     */
    protected function getEntityValueAndRemapId($value_name, Entity $Entity)
    {
        if (strcasecmp($value_name, $this->getTable()->getPk()) === 0) {
            $value = $Entity->getId();
        }
        else {
            $value = $Entity->getValueByName($value_name);
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function add(Entity $Entity)
    {
        list($sql, $parameters) = $this->getInsertSql($Entity);
        $PdoAdapter = $this->getSchema()->getPdoAdapterByName($this->write_connection_name);
        $id = $PdoAdapter->insert($sql, $parameters);
        return $this->getTable()->validateId($id);
    }

    /**
     * @inheritdoc
     */
    public function save(Entity $Entity)
    {
        $this->getTable()->validateId($Entity->getId());
        list($sql, $parameters) = $this->getUpdateSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->update($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function delete(Entity $Entity)
    {
        $this->getTable()->validateId($Entity->getId());
        list($sql, $parameters) = $this->getDeleteSql($Entity);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->delete($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function fetchOneById($id)
    {
        $Criteria = new DataMapper\Criteria();
        $Criteria->limit(1);
        $id = $this->getTable()->validateId($id);
        $Criteria->where([$this->getTable()->getPk() => $id]);
        $sql = $this->getFetchAllSql($Criteria);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $Criteria->getWhere())->fetch();
    }

    /**
     * @inheritdoc
     */
    public function fetchOneByCriteria(Criteria $Criteria)
    {
        $Criteria->limit(1);
        $sql = $this->getFetchAllSql($Criteria);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $Criteria->getWhere())->fetch();
    }

    /**
     * @inheritdoc
     */
    public function fetchAll(Criteria $Criteria)
    {
        $sql = $this->getFetchAllSql($Criteria);
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $Criteria->getWhere())->fetchAll();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getTable()->getName();
    }

    /**
     * @inheritdoc
     */
    public function getAndValidateId($data)
    {
        $pk_name = $this->getTable()->getPk();
        $id = @$data[$pk_name];
        return $this->validateId($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function validateId($id)
    {
        return $this->getTable()->validateId($id);
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->Table;
    }

    /**
     * @param Table $Table
     */
    public function setTable(Table $Table)
    {
        $this->Table = $Table;
    }

}