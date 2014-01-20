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

use Everon\DataMapper\Interfaces;
use Everon\DataMapper;
use Everon\Domain\Interfaces\Entity;


abstract class Mapper extends DataMapper 
{
    protected function getInsertSql(Entity $Entity)
    {
        $values_str = rtrim(implode(',', $this->getTable()->getPlaceholderForQuery()), ',');
        $sql = [];
        $sql[] = sprintf('INSERT INTO `%s.%s`', $this->getSchema()->getName(), $this->getTable()->getName());
        $sql[] = sprintf('VALUES (%s)', $values_str);
        $sql = implode("\n". $sql);

        return [$sql, $this->getTable()->getValuesForQuery($Entity)];
    }

    public function getUpdateSql(Entity $Entity)
    {
        $values = $this->getTable()->getValuesForQuery($Entity);
        $values_str = rtrim(implode('=', $values), '=');
        $sql = sprintf('UPDATE `%s.%s` SET '.$values_str.' WHERE %s = :id', $this->getSchema()->getName(), $this->getTable()->getName(), $this->getTable()->getPk());
        $params = $this->getTable()->getPlaceholderForQuery();
        $params[':id'] = $Entity->getId();
        return [$sql, $params];
    }

    public function getDeleteSql(Entity $Entity)
    {
        $sql = sprintf('DELETE FROM `%s.%s` WHERE %s = :id LIMIT 1', $this->getSchema()->getName(), $this->getTable()->getName(), $this->getTable()->getPk());
        $params = $this->getTable()->getPlaceholderForQuery();
        $params[':id'] = $Entity->getId();
        return [$sql, $params];
    }

    public function getFetchOneSql($id)
    {
        $sql = 'SELECT * FROM `%s.%s` WHERE %s = :id';
        $pk_name = $this->getTable()->getPk();
        $sql = sprintf($sql, $this->getSchema()->getName(), $this->getTable()->getName(), $pk_name);
        return [$sql, [':id' => $id]];
    }

    public function getFetchAllSql(array $criteria)
    {

    }
}