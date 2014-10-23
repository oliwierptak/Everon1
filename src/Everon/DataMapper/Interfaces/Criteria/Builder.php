<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Criteria;

use Everon\DataMapper\Interfaces;

interface Builder extends \Everon\Interfaces\Arrayable, \Everon\Interfaces\Stringable 
{
    /**
     * Starts new subquery
     * 
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    function where($column, $operator, $value);
        
    /**
     * Appends to current subquery
     * 
     * @param $column
     * @param $operator
     * @param $value
     * @return Builder
     */
    function andWhere($column, $operator, $value);

    /**
     * Appends to current subquery
     * 
     * @param $column
     * @param $operator
     * @param $value
     * @return Builder
     */
    function orWhere($column, $operator, $value);

    /**
     * @param $sql
     * @return $this
     */
    function whereRaw($sql);

    /**
     * @param $sql
     * @return $this
     */
    function andWhereRaw($sql);

    /**
     * @param $sql
     * @return $this
     */
    function orWhereRaw($sql);

    /**
     * @return Interfaces\Criteria\Container
     */
    function getCurrentContainer();

    /**
     * @param Interfaces\Criteria\Container $Container
     */
    function setCurrentContainer(Interfaces\Criteria\Container $Container);

    /**
     * @return \Everon\Interfaces\Collection
     */
    function getContainerCollection();

    /**
     * @param \Everon\Interfaces\Collection $ContainerCollection
     */
    function setContainerCollection(\Everon\Interfaces\Collection $ContainerCollection);

    /**
     * @return string
     */
    function getGlue();

    function resetGlue();

    function glueByAnd();

    function glueByOr();

    /**
     * @return string
     */
    function getGroupBy();
        
    /**
     * @param string $group_by
     */
    function setGroupBy($group_by);

    /**
     * @return int
     */
    function getLimit();

    /**
     * @param int $limit
     */
    function setLimit($limit);

    /**
     * @return int
     */
    function getOffset();

    /**
     * @param int $offset
     */
    function setOffset($offset);
    
    /**
     * @return array
     */
    function getOrderBy();

    /**
     * @param array $order_by
     */
    function setOrderBy(array $order_by);
    
    /**
     * @return Interfaces\SqlPart
     */
    function toSqlPart();

    /**
     * @param $operator
     * @return string
     * @throws \Everon\DataMapper\Exception\CriteriaBuilder
     */
    static function getOperatorClassNameBySqlOperator($operator);

    /**
     * @param $name
     * @return string
     */
    static function randomizeParameterName($name);
}
