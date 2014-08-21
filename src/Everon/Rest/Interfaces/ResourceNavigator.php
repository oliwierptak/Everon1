<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;


interface ResourceNavigator extends Dependency\Request
{
    /**
     * @param array $expand
     */
    function setExpand($expand);

    /**
     * @return array
     */
    function getExpand();

    /**
     * @param array $fields
     */
    function setFields(array $fields);

    /**
     * @return array
     */
    function getFields();

    /**
     * @param array $order_by
     */
    function setOrderBy(array $order_by);

    /**
     * @return array
     */
    function getOrderBy();

    /**
     * @param int $offset
     */
    function setOffset($offset);

    /**
     * @return int
     */
    function getOffset();
        
    /**
     * @param int $limit
     */
    function setLimit($limit);

    /**
     * @return int
     */
    function getLimit();


    /**
     * @return array
     */
    function getFilters();

    /**
     * @param array $filters
     */
    function setFilters(array $filters);

    /**
     * @return \Everon\DataMapper\Interfaces\Criteria
     */
    function toCriteria();
}