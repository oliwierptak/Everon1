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

interface Criteria extends \Everon\Interfaces\Arrayable
{
    /**
     * @param $offset
     * @return \Everon\DataMapper\Interfaces\Criteria
     */
    function offset($offset);

    function getOrderByAndSortSql();

    /**
     * @param $order_by
     * @return \Everon\DataMapper\Interfaces\Criteria
     */
    function orderBy($order_by);

    /**
     * @param $group_by
     * @return $this
     */
    function groupBy($group_by);

    function getOffsetLimitSql();

    function getWhereSql();

    /**
     * @param array $where
     * @return \Everon\DataMapper\Interfaces\Criteria
     */
    function where(array $where);

    /**
     * @param array $where_or
     * @return \Everon\DataMapper\Interfaces\Criteria
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

    function sort($sort);

    /**
     * @param $limit
     * @return \Everon\DataMapper\Interfaces\Criteria
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
     * @return string
     */
    function getSort();

    /**
     * @return array
     */
    function getWhere();

}
