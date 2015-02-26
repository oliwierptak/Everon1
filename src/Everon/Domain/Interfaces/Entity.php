<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Interfaces;

use Everon\Interfaces;
use Everon\Interfaces\Collection;


interface Entity extends Interfaces\Arrayable
{

    /**
     * @return bool
     */
    function isNew();

    /**
     * @return bool
     */
    function isModified();

    /**
     * @return bool
     */
    function isPersisted();

    /**
     * @return bool
     */
    function isDeleted();

    /**
     * @param $name
     * @return bool
     */
    function isPropertyModified($name);

    /**
     * @return mixed
     */
    function getId();

    /**
     * @return array
     */
    function getModifiedProperties();

    /**
     * @param $name
     * @return mixed
     * @throws \Everon\Domain\Exception\Entity
     */
    function getValueByName($name);

    /**
     * @param $name
     * @param mixed $value
     */
    function setValueByName($name, $value);

    /**
     * @param array $data
     */
    function persist(array $data);

    /**
     * @return void
     */
    function delete();
    
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
     * @param Relation $Relation
     * @return mixed
     */
    function setRelationByName($name, Relation $Relation);

    /**
     * @param $name
     * @return Relation
     */
    function getRelationByName($name);

    /**
     * @param $name
     * @return bool
     */
    function hasRelation($name);

    /**
     * @return string
     */
    function getDomainName();

    /**
     * @return array
     */
    function getRelationDefinition();

    /**
     * @param array $relation_definition
     */
    function setRelationDefinition(array $relation_definition);

    /**
     * @param $name
     */
    function resetRelationState($name);

    /**
     * @param array $data
     */
    function updateValues(array $data);
}
