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
    
    abstract protected function getInsertSql(array $data);
    abstract protected function getUpdateSql(array $data);
    abstract protected function getDeleteSql($id);
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
    protected function getPlaceholderForQuery($placeholder=':')
    {
        $placeholders = [];
        $columns = $this->getTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            if ($Column->isPk()) {
                continue;
            }
            $placeholders[] = $placeholder.$name;
        }

        return $placeholders;
    }

    /**
     * @param array $data
     * @param string $delimiter
     * @return array
     */
    protected function getValuesForQuery(array $data, $delimiter='')
    {
        $values = [];
        $columns = $this->getTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            if ($Column->isPk()) {
                continue;
            }
            $values[$delimiter.$name] = $data[$name];
        }

        return $values;
    }

    /**
     * @inheritdoc
     */
    public function add(array $data)
    {
        list($sql, $parameters) = $this->getInsertSql($data);
        $PdoAdapter = $this->getSchema()->getPdoAdapterByName($this->write_connection_name);
        $primary_keys = $this->getTable()->getPrimaryKeys();
        /**
         * @var DataMapper\Interfaces\Schema\PrimaryKey $PrimaryKey
         */
        $PrimaryKey = $primary_keys[$this->getTable()->getPk()];
        
        $id = $PdoAdapter->insert($sql, $parameters, $PrimaryKey->getSequenceName());
        $id = $this->getTable()->validateId($id);
        $data[$this->getTable()->getPk()] = $id;
        
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        list($sql, $parameters) = $this->getUpdateSql($data);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->update($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        list($sql, $parameters) = $this->getDeleteSql($id);
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
    public function getIdFromData($data)
    {
        $pk_name = $this->getTable()->getPk();
        if (isset($data[$pk_name]) === false) {
            return null;
        }
        
        return $data[$pk_name];
    }

    /**
     * @inheritdoc
     */
    public function validateData(array $data, $validate_id)
    {
        $entity_data = [];
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($this->getTable()->getColumns() as $name => $Column) {
            if ($Column->isPk()) {
                $entity_data[$name] = null;
                continue;
            }

            $value = $this->getTable()->validateColumnValue($name, $data[$name]);
            $entity_data[$name] = $value;
        }
        
        if ($validate_id) {
            $pk_name = $this->getTable()->getPk();
            $id = $this->getIdFromData($data);
            $id = $this->getTable()->validateId($id);
            $entity_data[$pk_name] = $id;
        }
        
        return $entity_data;
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