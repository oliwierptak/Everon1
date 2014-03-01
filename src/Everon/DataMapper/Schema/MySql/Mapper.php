<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\MySql;

use Everon\DataMapper;
use Everon\DataMapper\Interfaces;
use Everon\Domain\Interfaces\Entity;


abstract class Mapper extends DataMapper 
{
    protected function getInsertSql(Entity $Entity)
    {
        $values_str = rtrim(implode(',', $this->getPlaceholderForQuery()), ',');
        $columns_str = rtrim(implode(',', $this->getPlaceholderForQuery('')), ',');
        $sql = sprintf('INSERT INTO `%s`.`%s` (%s) VALUES (%s)', $this->getSchema()->getDatabase(), $this->getTable()->getName(), $columns_str, $values_str);
        return [$sql, $this->getValuesForQuery($Entity)];
    }

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
        $sql = sprintf('UPDATE `%s`.`%s` SET '.$values_str.' WHERE %s = :%s', $this->getSchema()->getDatabase(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = $this->getValuesForQuery($Entity);
        return [$sql, $params];
    }

    protected function getDeleteSql(Entity $Entity)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf('DELETE FROM `%s`.`%s` WHERE %s = :%s LIMIT 1', $this->getSchema()->getDatabase(), $this->getTable()->getName(), $pk_name, $pk_name);
        $params = [$pk_name => $Entity->getId()];
        return [$sql, $params];
    }

    protected function getFetchAllSql(Interfaces\Criteria $Criteria)
    {
        $pk_name = $this->getTable()->getPk();
        list($where_str, $parameters) = $Criteria->getWhereSql();
        $where_str = $where_str === '' ?: 'WHERE '.$where_str;
        $offset_limit_sql = $Criteria->getOffsetLimitSql();
        $order_by_str = $Criteria->getOrderByAndSortSql();
        
        $sql = '
            SELECT * 
            FROM `%s`.`%s`
            '.$where_str.'
            '.$order_by_str.'
             '.$offset_limit_sql.'
        ';
        
        $sql = sprintf($sql, $this->getSchema()->getDatabase(), $this->getTable()->getName(), $pk_name);
        return [$sql, $parameters];
    }
}