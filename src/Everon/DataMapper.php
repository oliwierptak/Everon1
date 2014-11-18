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

abstract class DataMapper implements Interfaces\DataMapper
{
    use Dependency\Injection\Factory;
    use DataMapper\Dependency\Schema;
    
    use Helper\String\LastTokenToName;
    

    /**
     * @var DataMapper\Interfaces\Schema\Table
     */
    protected $Table = null;

    protected $name = null;
    
    protected $write_connection_name = 'write';
    
    protected $read_connection_name = 'read';
    

    /**
     * @inheritdoc
     */
    abstract public function getInsertSql();
    
    /**
     * @inheritdoc
     */
    abstract public function getUpdateSql();

    /**
     * @inheritdoc
     */
    abstract public function getDeleteSql();
    
    /**
     * @inheritdoc
     */
    abstract public function getFetchAllSql();

    /**
     * @inheritdoc
     */
    abstract public function getJoinSql($select, $a, $b, $on_a, $on_b, $type='');

    /**
     * @inheritdoc
     */
    abstract public function getCountSql();
    

    /**
     * @param DataMapper\Interfaces\Schema\Table $Table
     * @param DataMapper\Interfaces\Schema $Schema
     */
    public function __construct(DataMapper\Interfaces\Schema\Table $Table, DataMapper\Interfaces\Schema $Schema)
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
     * @param array $data should be in format required by db, all DateTime objects and alike should be gone
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
        $sql = $this->getInsertSql();
        //$parameters = $this->getTable()->prepareDataForSql($data, false);//data should be prepared at this point

        unset($data[$this->getTable()->getPk()]);
        $id = $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->insert($sql, $data);
        $id = $this->getTable()->validateId($id);
        
        $data[$this->getTable()->getPk()] = $id;
        
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        //$parameters = $this->getTable()->prepareDataForSql($data, true);//data should be prepared at this point
        $id = $this->getTable()->getIdFromData($data);
        $id = $this->getTable()->validateId($id);
        
        $data[$this->getTable()->getPk()] = $id;

        //$CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        //$CriteriaBuilder->where($this->getTable()->getPk(), '=', $id);
        
        //$SqlPart = $CriteriaBuilder->toSqlPart();
        //$sql = trim($this->getUpdateSql().' '.$SqlPart->getSql());
        $sql = trim($this->getUpdateSql().' WHERE '.$this->getTable()->getPk() .' = :'.$this->getTable()->getPk());
        //$parameters = array_merge($data, $SqlPart->getParameters());
        
        //$parameters[$this->getTable()->getPk()] = null;
        //unset($parameters[$this->getTable()->getPk()]);
        
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->update($sql, $data);
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        $id = $this->getTable()->validateId($id);
        $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        $CriteriaBuilder->where($this->getTable()->getPk(), '=', $id);
        
        $SqlPart = $CriteriaBuilder->toSqlPart();
        $sql = trim($this->getDeleteSql().' '.$SqlPart->getSql());
        
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->delete($sql, $SqlPart->getParameters());
    }

    /**
     * @inheritdoc
     */
    public function deleteByCriteria(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $SqlPart = $CriteriaBuilder->toSqlPart();
        if (empty($SqlPart->getParameters())) {
            throw new Exception\DataMapper('No criteria conditions defined');
        }

        $sql = trim($this->getDeleteSql().' '.$SqlPart->getSql());
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->delete($sql, $SqlPart->getParameters());
    }

    /**
     * @inheritdoc
     */
    public function count(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder=null)
    {
        if ($CriteriaBuilder === null) {
            $Criteria = $this->getFactory()->buildCriteriaBuilder();
        }
        else {
            $Criteria = clone $CriteriaBuilder;
            $Criteria->setOrderBy([]);
            $Criteria->setOffset(null);
            $Criteria->setLimit(null);
        }

        $SqlPart = $Criteria->toSqlPart();
        
        $sql = trim($this->getCountSql().' '.$SqlPart->getSql());
        //sd($sql, $SqlPart->getParameters());
        $PdoStatement = $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $SqlPart->getParameters());
        return (int) $PdoStatement->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function fetchOneById($id)
    {
        $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        $id = $this->getTable()->validateId($id);
        $CriteriaBuilder->where($this->getTable()->getPk(), '=', $id);
        
        return $this->fetchOneByCriteria($CriteriaBuilder);
    }

    /**
     * @inheritdoc
     */
    public function fetchOneByCriteria(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $CriteriaBuilder->setLimit(1);
        $SqlPart = $CriteriaBuilder->toSqlPart();
        
        $sql = trim($this->getFetchAllSql().' '.$SqlPart->getSql());
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $SqlPart->getParameters())->fetch();
    }

    /**
     * @inheritdoc
     */
    public function fetchAll(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $SqlPart = $CriteriaBuilder->toSqlPart();
        $sql = trim($this->getFetchAllSql().' '.$SqlPart->getSql());
        
        return $this->getSchema()->getPdoAdapterByName($this->read_connection_name)->execute($sql, $SqlPart->getParameters())->fetchAll();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->name = $this->stringLastTokenToName(get_called_class());
        }
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return $this->Table;
    }

    /**
     * @inheritdoc
     */
    public function setTable(DataMapper\Interfaces\Schema\Table $Table)
    {
        $this->Table = $Table;
    }

    /**
     * @inheritdoc
     */
    public function setReadConnectionName($read_connection_name)
    {
        $this->read_connection_name = $read_connection_name;
    }

    /**
     * @inheritdoc
     */
    public function getReadConnectionName()
    {
        return $this->read_connection_name;
    }

    /**
     * @inheritdoc
     */
    public function setWriteConnectionName($write_connection_name)
    {
        $this->write_connection_name = $write_connection_name;
    }

    /**
     * @inheritdoc
     */
    public function getWriteConnectionName()
    {
        return $this->write_connection_name;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->commitTransaction();
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction()
    {
        $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->rollbackTransaction();
    }

}