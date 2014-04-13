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
    
    use Helper\String\LastTokenToName;
    

    /**
     * @var Table
     */
    protected $Table = null;
    
    protected $name = null;
    
    protected $write_connection_name = 'write';
    protected $read_connection_name = 'read';
    
    abstract protected function getInsertSql(array $data);
    abstract protected function getUpdateSql($id, array $data);
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
     * @param $user_id
     * @return mixed
     */
    protected function setCurrentUserId($user_id)
    {
        //SELECT session_variables.set_value('who', 'depesz');
        //SELECT session_variables.get_value('who');

        $sql = "SELECT s_sessions.set_value('AUDIT_USER_ID', '${user_id}')";
        $PdoAdapter = $this->getSchema()->getPdoAdapterByName($this->write_connection_name);
        return $PdoAdapter->execute($sql)->fetch();
    }
    
    /**
     * @inheritdoc
     */
    public function add(array $data, $user_id)
    {
        $this->setCurrentUserId($user_id);
        $data = $this->getTable()->validateData($data, false);
        list($sql, $parameters) = $this->getInsertSql($data);
        $PdoAdapter = $this->getSchema()->getPdoAdapterByName($this->write_connection_name);
        $id = $PdoAdapter->insert($sql, $parameters);
        $id = $this->getTable()->validateId($id);
        $data[$this->getTable()->getPk()] = $id;
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data, $user_id)
    {
        $this->setCurrentUserId($user_id);
        $data = $this->getTable()->validateData($data, true);
        $id = $this->getTable()->getIdFromData($data);
        $id = $this->getTable()->validateId($id);

        list($sql, $parameters) = $this->getUpdateSql($id, $data);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->update($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function delete($id, $user_id)
    {
        $this->setCurrentUserId($user_id);
        $id = $this->getTable()->validateId($id);
        list($sql, $parameters) = $this->getDeleteSql($id);
        return $this->getSchema()->getPdoAdapterByName($this->write_connection_name)->delete($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function fetchOneById($id)
    {
        $Criteria = new DataMapper\Criteria();
        $id = $this->getTable()->validateId($id);
        $Criteria->where([$this->getTable()->getPk() => $id]);
        return $this->fetchOneByCriteria($Criteria);
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
        if ($this->name === null) {
            $this->name = $this->stringLastTokenToName(get_class($this));
        }
        return $this->name;
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