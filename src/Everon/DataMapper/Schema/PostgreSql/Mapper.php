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
     * @param Entity $Entity
     * @return array
     */
    protected function getInsertSql(Entity $Entity)
    {
        $values_str = rtrim(implode(',', $this->getPlaceholderForQuery()), ',');
        $columns_str = rtrim(implode(',', $this->getPlaceholderForQuery('')), ',');
        $sql = sprintf('INSERT INTO %s.%s (%s) VALUES (%s)', $this->getTable()->getSchema(), $this->getTable()->getName(), $columns_str, $values_str);
        return [$sql, $this->getValuesForQuery($Entity)];
    }

    /**
     * @param Entity $Entity
     * @return array
     */
    protected function getUpdateSql(Entity $Entity)
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
        $params = $this->getValuesForQuery($Entity);
        return [$sql, $params];
    }

    /**
     * @param Entity $Entity
     * @return array
     */
    protected function getDeleteSql(Entity $Entity)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf('DELETE FROM %s.%s WHERE %s = :%s LIMIT 1', $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = [$pk_name => $Entity->getId()];
        return [$sql, $params];
    }

    /**
     * @param Interfaces\Criteria $Criteria
     * @return array
     */
    protected function getFetchAllSql(Interfaces\Criteria $Criteria)
    {
        $pk_name = $this->getTable()->getPk();

        $sql = '
            SELECT * 
            FROM %s.%s
            WHERE
            '.$Criteria;
        
        $sql = sprintf($sql, $this->getTable()->getSchema(), $this->getTable()->getName(), $pk_name);
        return [$sql, $Criteria->getWhere()];
    }

    protected function getLeftJoinSql($select, $a, $b, $on_a, $on_b, Interfaces\Criteria $Criteria)
    {
        $sql = '
            SELECT %s FROM %s
            LEFT JOIN %s ON %s = %s 
            WHERE
            '.$Criteria;
        
        $sql = sprintf($sql, $select, $a, $b, $on_a, $on_b);
        return [$sql, $Criteria->getWhere()];
    }
}