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
        $sql = sprintf('INSERT INTO `%s`.`%s` (%s) VALUES (%s)', $this->getSchema()->getName(), $this->getSchemaTable()->getName(), $columns_str, $values_str);
        return [$sql, $this->getValuesForQuery($Entity)];
    }

    protected function getUpdateSql(Entity $Entity)
    {
        $pk_name = $this->getSchemaTable()->getPk();
        $values_str = '';
        $columns = $this->getSchemaTable()->getColumns();
        /**
         * @var DataMapper\Interfaces\Schema\Column $Column
         */
        
        foreach ($columns as $Column) {
            if ($Column->isPk() === false) {
                $values_str .= $Column->getName().' = :'.$Column->getName().',';
            }
        }
        
        $values_str = rtrim($values_str, ',');
        $sql = sprintf('UPDATE `%s`.`%s` SET '.$values_str.' WHERE %s = :%s', $this->getSchema()->getName(), $this->getSchemaTable()->getName(), $pk_name, $pk_name);
        $params = $this->getValuesForQuery($Entity);
        return [$sql, $params];
    }

    protected function getDeleteSql(Entity $Entity)
    {
        $pk_name = $this->getSchemaTable()->getPk();
        $sql = sprintf('DELETE FROM `%s`.`%s` WHERE %s = :%s LIMIT 1', $this->getSchema()->getName(), $this->getSchemaTable()->getName(), $pk_name, $pk_name);
        $params = [$pk_name => $Entity->getId()];
        return [$sql, $params];
    }

    protected function getFetchOneSql($id)
    {
        $pk_name = $this->getSchemaTable()->getPk();
        $sql = 'SELECT * FROM `%s`.`%s` WHERE %s = :%s';
        $sql = sprintf($sql, $this->getSchema()->getName(), $this->getSchemaTable()->getName(), $pk_name, $pk_name);
        return [$sql, [$pk_name => $id]];
    }

    protected function getFetchAllSql(Interfaces\Criteria $Criteria)
    {
        $pk_name = $this->getSchemaTable()->getPk();
        $sql = '
            SELECT 
                * 
            FROM `%s`.`%s`
            ORDER BY %s
            LIMIT %d
            OFFSET %d
        ';
        $sql = sprintf($sql, $this->getSchema()->getName(), $this->getSchemaTable()->getName(), $pk_name, 10, 0);
        return [$sql, []];
    }
}