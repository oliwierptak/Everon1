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
     * @param $glue
     * @return $this
     */
    function where($column, $operator, $value, $glue = \Everon\DataMapper\Criteria\Builder::GLUE_AND);
        
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
     * @param array|null $value
     * @param string $glue
     * @return $this
     */
    function whereRaw($sql, array $value = null, $glue = \Everon\DataMapper\Criteria\Builder::GLUE_AND);

    /**
     * @param $sql
     * @param null $value
     * @return $this
     */
    function andWhereRaw($sql, $value = null);

    /**
     * @param $sql
     * @param $value
     * @return $this
     */
    function orWhereRaw($sql, $value = null);

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
     * @return Interfaces\Criteria\Builder
     */
    function setGroupBy($group_by);

    /**
     * @return int
     */
    function getLimit();

    /**
     * @param int $limit
     * @return Interfaces\Criteria\Builder
     */
    function setLimit($limit);

    /**
     * @return int
     */
    function getOffset();

    /**
     * @param int $offset
     * @return Interfaces\Criteria\Builder
     */
    function setOffset($offset);
    
    /**
     * @return array
     */
    function getOrderBy();

    /**
     * @param array $order_by
     * @return Interfaces\Criteria\Builder
     */
    function setOrderBy(array $order_by);
    
    /**
     * @return Interfaces\SqlPart
     */
    function toSqlPart();

    /**
     * @param \Everon\Interfaces\Collection $ContainerCollectionToMerge
     * @param string $glue
     */
    function appendContainerCollection(\Everon\Interfaces\Collection $ContainerCollectionToMerge, $glue=\Everon\DataMapper\Criteria\Builder::GLUE_AND);

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

    /**
     * @return string
     */
    function getOffsetLimitSql();

    /**
     * @return string
     */
    function getOrderByAndSortSql();

    /**
     * @return string
     */
    function getGroupBySql();
}
