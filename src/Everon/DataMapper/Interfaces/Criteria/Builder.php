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
     * @return Interfaces\Criteria
     */
    function getCurrentCriteria();

    /**
     * @param Interfaces\Criteria $Criteria
     */
    function setCurrentCriteria(Interfaces\Criteria $Criteria);

    /**
     * @return string
     */
    function getGlue();

    /**
     * @param string $glue
     */
    function setGlue($glue);

    /**
     * @return \Everon\Interfaces\Collection
     */
    function getCriteriaCollection();

    /**
     * @param \Everon\Interfaces\Collection $CriteriaCollection
     */
    function setCriteriaCollection(\Everon\Interfaces\Collection $CriteriaCollection);

    /**
     * @return Interfaces\SqlPart
     */
    public function toSqlPart();

    function glueByAnd();

    function glueByOr();

    /**
     * @param $operator
     * @return string
     * @throws \Everon\DataMapper\Exception\CriteriaBuilder
     */
    static function getOperatorClassName($operator);
}
