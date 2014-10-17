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

use Everon\DataMapper\Interfaces\CriteriaOLD;

interface Filter
{
    /**
     * @param \Everon\Helper\Collection $Collection
     */
    function setFilterCollection(\Everon\Helper\Collection $Collection);

    /**
     * @return \Everon\Helper\Collection
     */
    function getFilterCollection();

    /**
     * @param \Everon\Helper\Collection $Collection
     */
    function setFilterDefinition(\Everon\Helper\Collection $Collection);

    /**
     * @return \Everon\Helper\Collection
     */
    function getFilterDefinition();

    /**
     * @param CriteriaOLD $Criteria
     * @return mixed
     */
    function assignToCriteria(CriteriaOLD $Criteria);
}