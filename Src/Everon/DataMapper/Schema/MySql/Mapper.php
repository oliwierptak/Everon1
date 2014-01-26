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
        $sql = sprintf('INSERT INTO `%s.%s` VALUES (%s)', $this->getSchema()->getName(), $this->getTable()->getName(), $values_str);
        return [$sql, $this->getValuesForQuery($Entity)];
    }

    public function getUpdateSql(Entity $Entity)
    {
        $pk_name = $this->getTable()->getPk();
        $values = $this->getValuesForQuery($Entity);
        $values_str = rtrim(implode('=', $values), '=');
        $sql = sprintf('UPDATE `%s.%s` SET '.$values_str.' WHERE %s = :id', $this->getSchema()->getName(), $this->getTable()->getName(), $pk_name);
        $params = $this->getPlaceholderForQuery();
        $params[":${pk_name}"] = $Entity->getId();
        return [$sql, $params];
    }

    public function getDeleteSql(Entity $Entity)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf('DELETE FROM `%s.%s` WHERE %s = :id LIMIT 1', $this->getSchema()->getName(), $this->getTable()->getName(), $pk_name);
        $params = $this->getPlaceholderForQuery();
        $params[":${pk_name}"] = $Entity->getId();
        return [$sql, $params];
    }

    public function getFetchOneSql($id)
    {
        $pk_name = $this->getTable()->getPk();
        $sql = 'SELECT * FROM `%s.%s` WHERE %s = :id';
        $sql = sprintf($sql, $this->getSchema()->getName(), $this->getTable()->getName(), $pk_name);
        return [$sql, [":${pk_name}" => $id]];
    }

    public function getFetchAllSql(array $criteria)
    {

    }
}