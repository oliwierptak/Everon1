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

use Everon\Domain\Interfaces\Entity;
use Everon\Interfaces\Collection;


interface Resource extends ResourceBasic
{
    /**
     * @return Entity
     */
    function getDomainEntity();

    /**
     * @return array
     */
    function getRelationDefinition();

    /**
     * @param Collection $RelationCollection
     */
    function setRelationCollection(Collection $RelationCollection);

    /**
     * @return Collection
     */
    function getRelationCollection();
}