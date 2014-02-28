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
    function offset($offset);

    function getOrderByAndSortSql();

    function orderBy($order_by);

    function getOffsetLimitSql();

    function getWhereSql();

    function sortDesc();

    function where(array $where);

    function sortAsc();

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
