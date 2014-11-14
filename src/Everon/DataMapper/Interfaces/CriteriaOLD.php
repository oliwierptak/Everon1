<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

interface CriteriaOLD extends \Everon\Interfaces\Arrayable, \Everon\Interfaces\Stringable 
{
    /**
     * @param $offset
     * @return \Everon\DataMapper\Interfaces\CriteriaOLD
     */
    function offset($offset);

    function getOrderByAndSortSql();

    /**
     * @param array $order_by ['field' => 'ASC', 'another_field' => 'DESC']
     * @return \Everon\DataMapper\Interfaces\CriteriaOLD
     */
    function orderBy(array $order_by);

    /**
     * @param $group_by
     * @return $this
     */
    function groupBy($group_by);

    function getOffsetLimitSql();

    function getWhereSql();

    /**
     * @param array $where
     * @return \Everon\DataMapper\Interfaces\CriteriaOLD
     */
    function where(array $where);

    /**
     * @param array $where_or
     * @return \Everon\DataMapper\Interfaces\CriteriaOLD
     */
    function whereOr(array $where_or);

    /**
     * @param array $in
     * @return $this
     */
    function in(array $in);

    /**
     * @param array $ilike
     * @return $this
     */
    function ilike(array $ilike);

    /**
     * @param array $filter
     * @return $this
     */
    function filter(array $filter);

    /**
     * @param $limit
     * @return \Everon\DataMapper\Interfaces\CriteriaOLD
     */
    function limit($limit);

    /**
     * @return int
     */
    function getLimit();

    /**
     * @return int
     */
    function getOffset();

    /**
     * @return string
     */
    function getOrderBy();

    /**
     * @return array
     */
    function getWhere();
}