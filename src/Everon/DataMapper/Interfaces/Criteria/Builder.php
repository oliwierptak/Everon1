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
     * @param $column
     * @param $operator
     * @param $value
     * @return Builder
     */
    function andWhere($column, $operator, $value);

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return Builder
     */
    function orWhere($column, $operator, $value);

    /**
     * @return Interfaces\Criteria
     */
    function getCriteria();

    /**
     * @param Interfaces\Criteria $Criteria
     */
    function setCriteria(Interfaces\Criteria $Criteria);

    /**
     * @return string
     */
    function getGlue();

    /**
     * @param string $glue
     */
    function setGlue($glue);
}
