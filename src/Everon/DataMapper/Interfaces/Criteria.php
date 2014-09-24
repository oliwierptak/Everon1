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
    function _and(Criteria\Criterium $Criterium);

    function _or(Criteria\Criterium $Criterium);

    /**
     * @return \Everon\Interfaces\Collection
     */
    function getCriteriumCollection();

    /**
     * @param \Everon\Interfaces\Collection $CriteriumCollection
     */
    function setCriteriumCollection($CriteriumCollection);

    /**
     * @return string
     */
    function getGlue();

    /**
     * @param string $glue
     */
    function setGlue($glue);
}