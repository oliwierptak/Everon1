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
    public function getInsertSql()
    {
        $values_str = rtrim(implode(',', $this->getPlaceholderForQuery()), ',');
        $columns = $this->getPlaceholderForQuery('');
        array_walk($columns, function(&$item) {
            $item = '"'.$item.'"';
        });
        $columns_str = rtrim(implode(',', $columns), ',');
        return sprintf('INSERT INTO %s.%s (%s) VALUES (%s) RETURNING %s', $this->getTable()->getSchema(), $this->getTable()->getName(), $columns_str, $values_str, $this->getTable()->getPk());
    }

    /**
     * @inheritdoc
     */
    public function getUpdateSql()
    {
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
        return sprintf('UPDATE %s.%s t SET %s', $this->getTable()->getSchema(), $this->getTable()->getName(), $values_str);
    }

    /**
     * @inheritdoc
     */
    public function getDeleteSql()
    {
        return sprintf('DELETE FROM %s.%s t', $this->getTable()->getSchema(), $this->getTable()->getName());
    }

    /**
     * @inheritdoc
     */
    public function getFetchAllSql()
    {
        $sql = "SELECT * FROM %s.%s t";
        
        return sprintf($sql, $this->getTable()->getSchema(), $this->getTable()->getName());
    }

    /**
     * @inheritdoc
     */
    public function getJoinSql($select, $a, $b, $on_a, $on_b, $type='')
    {
        $sql = "SELECT %s FROM %s t ${type} JOIN %s ON %s = %s";
        
        return sprintf($sql, $select, $a, $b, $on_a, $on_b);
    }

    /**
     * @inheritdoc
     */
    public function getCountSql()
    {
/*        
        $sql = "SELECT pgc.reltuples AS total_count FROM pg_catalog.pg_class AS pgc "; 
        $sql .= $where_str.' pgc.oid = '.sprintf("'${table_name}'::regclass", $this->getTable()->getSchema(), $this->getTable()->getName());*/

        //do slow count
        return sprintf("SELECT COUNT(t.%s) FROM %s.%s t", $this->getTable()->getPk(), $this->getTable()->getSchema(), $this->getTable()->getName());
    }
}