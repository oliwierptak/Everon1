<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\PostgreSql;

use Everon\DataMapper;
use Everon\DataMapper\Interfaces;


abstract class Mapper extends DataMapper 
{
    /**
     * @inheritdoc
     */
    public function getInsertSql(array $data)
    {
        $data = $this->getTable()->prepareDataForSql($data, false);
        $values_str = rtrim(implode(',', $this->getPlaceholderForQuery()), ',');
        $columns = $this->getPlaceholderForQuery('');
        array_walk($columns, function(&$item) {
            $item = '"'.$item.'"';
        });
        $columns_str = rtrim(implode(',', $columns), ',');
        $sql = sprintf('INSERT INTO %s.%s (%s) VALUES (%s) RETURNING %s', $this->getTable()->getSchema(), $this->getTable()->getName(), $columns_str, $values_str, $this->getTable()->getPk());
        return [$sql, $this->getValuesForQuery($data)];
    }

    /**
     * @inheritdoc
     */
    public function getUpdateSql(array $data)
    {
        $data = $this->getTable()->prepareDataForSql($data, true);
        $id = $this->getTable()->getIdFromData($data);
        $id = $this->getTable()->validateId($id);
        
        $pk_name = $this->getTable()->getPk();
        $values_str = '';
        $columns = $this->getTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($columns as $name => $Column) {
            if ($Column->isPk() === false) {
                $values_str .= $name.' = :'.$name.',';
            }
        }

        $values_str = rtrim($values_str, ',');
        $sql = sprintf('UPDATE %s.%s t SET '.$values_str.' WHERE %s = :%s', $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = $this->getValuesForQuery($data);
        $params[$pk_name] = $id;
        return [$sql, $params];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteSql($id)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf('DELETE FROM %s.%s t WHERE %s = :%s', $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = [$pk_name => $id];
        return [$sql, $params];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteByCriteriaSql(Interfaces\Criteria $Criteria)
    {
        $params = $Criteria->getWhere();
        if (empty($params)) {
            throw new \Everon\Exception\DataMapper('No criteria conditions defined');
        }
        
        $sql = sprintf('DELETE FROM %s.%s t %s', $this->getTable()->getSchema(), $this->getTable()->getName(), $Criteria->getWhereSql());
        return [$sql, $params];
    }

    /**
     * @inheritdoc
     */
    public function getFetchAllSql(Interfaces\Criteria $Criteria=null)
    {
        $pk_name = $this->getTable()->getPk();

        $sql = "
            SELECT * 
            FROM %s.%s t
            ";
        $sql .= $Criteria;
        
        $sql = sprintf($sql, $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name);
        return $sql;
    }

    /**
     * @inheritdoc
     */
    public function getJoinSql($select, $a, $b, $on_a, $on_b, Interfaces\Criteria $Criteria=null, $type='')
    {
        $sql = "
            SELECT %s FROM %s t
            ${type} JOIN %s ON %s = %s 
            ";
        $sql .= $Criteria;
        
        $sql = sprintf($sql, $select, $a, $b, $on_a, $on_b);
        return $sql;
    }

    /**
     * @inheritdoc
     */
    public function getCountSql(Interfaces\Criteria $Criteria=null)
    {
        $table_name = sprintf('%s.%s', $this->getTable()->getSchema(), $this->getTable()->getName());
/*        
        $sql = "SELECT pgc.reltuples AS total_count FROM pg_catalog.pg_class AS pgc "; 
        $sql .= $CriteriaOLD;
        $where_str = empty($CriteriaOLD->getWhere()) ? 'WHERE ' : ''; 
        $sql .= $where_str.' pgc.oid = '.sprintf("'${table_name}'::regclass", $this->getTable()->getSchema(), $this->getTable()->getName());*/
        
        //do slow count
        $pk = $this->getTable()->getPk();
        $sql = "SELECT COUNT(${pk}) FROM ${table_name} t ".$Criteria;

        return [$sql, $Criteria->getWhere()];
    }
}