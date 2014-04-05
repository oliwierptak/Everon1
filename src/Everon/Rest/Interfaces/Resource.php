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
     * @param Collection $RelationCollection
     */
    function setRelationCollection(Collection $RelationCollection);

    /**
     * @return Collection
     */
    function getRelationCollection();

    /**
     * @param $name
     * @param ResourceCollection $CollectionResource
     */
    function setRelationCollectionByName($name, ResourceCollection $CollectionResource);

    /**
     * @param $name
     * @return ResourceCollection
     */
    function getRelationCollectionByName($name);

    /**
     * @return string
     */
    function getDomainName();
}