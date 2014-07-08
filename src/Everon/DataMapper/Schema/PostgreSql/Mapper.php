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
use Everon\Domain\Interfaces\Entity;


abstract class Mapper extends DataMapper 
{
    /**
     * @param array $data
     * @return array
     */
    protected function getInsertSql(array $data)
    {
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
     * @param array $id
     * @param array $data
     * @return array
     */
    protected function getUpdateSql($id, array $data)
    {
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
        $sql = sprintf('UPDATE %s.%s SET '.$values_str.' WHERE %s = :%s', $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = $this->getValuesForQuery($data);
        $params[$pk_name] = $id;
        return [$sql, $params];
    }

    /**
     * @param $id
     * @return array
     */
    protected function getDeleteSql($id)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf('DELETE FROM %s.%s WHERE %s = :%s', $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = [$pk_name => $id];
        return [$sql, $params];
    }

    /**
     * @param Interfaces\Criteria $Criteria
     * @return array
     */
    protected function getFetchAllSql(Interfaces\Criteria $Criteria)
    {
        $pk_name = $this->getTable()->getPk();

        $sql = "
            SELECT * 
            FROM %s.%s
            ";
        $sql .= $Criteria;
        
        $sql = sprintf($sql, $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name);
        return $sql;
    }

    protected function getLeftJoinSql($select, $a, $b, $on_a, $on_b, Interfaces\Criteria $Criteria)
    {
        $sql = "
            SELECT %s FROM %s
            LEFT JOIN %s ON %s = %s 
            ";
        $sql .= $Criteria;
        
        $sql = sprintf($sql, $select, $a, $b, $on_a, $on_b);
        return $sql;
    }

    protected function getCountSql(Interfaces\Criteria $Criteria)
    {
        $table_name = sprintf('%s.%s', $this->getTable()->getSchema(), $this->getTable()->getName());
/*        
        $sql = "SELECT pgc.reltuples AS total_count FROM pg_catalog.pg_class AS pgc "; 
        $sql .= $Criteria;
        $where_str = empty($Criteria->getWhere()) ? 'WHERE ' : ''; 
        $sql .= $where_str.' pgc.oid = '.sprintf("'${table_name}'::regclass", $this->getTable()->getSchema(), $this->getTable()->getName());*/
        
        //do slow count
        $pk = $this->getTable()->getPk();
        $sql = "SELECT COUNT(${pk}) FROM ${table_name}";

        return [$sql, $Criteria->getWhere()];
    }
}