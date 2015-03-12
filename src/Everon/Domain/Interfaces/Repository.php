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

use Everon\Domain\Exception;

interface Repository
{
    /**
     * @param Entity $Entity
     * @return array
     */
    function validateEntity(Entity $Entity);

    /**
     * @param $id
     * @param  \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity|null
     */
    function getEntityById($id, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria=null);

    /**
     * @param array $property_criteria
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return array|null
     */
    function getOneByPropertyValue(array $property_criteria, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria = null);

    /**
     * @param array $property_criteria
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity|null
     */
    function getByPropertyValue(array $property_criteria, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria = null);

    /**
     * @param array $data
     * @param int $user_id
     * @return mixed
     */
    function persistFromArray(array $data, $user_id=null);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder
     * @return array|null
     */
    function getByCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder=null);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return int
     */
    function count(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder = null);

    /**
     * @param Entity $Entity
     * @param int $user_id
     */
    function persist(Entity $Entity, $user_id=null);

    /**
     * @param Entity
     * @param int $user_id
     */
    function remove(Entity $Entity, $user_id=null);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return
     */
    function removeByCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @param array $property_criteria
     */
    function removeByPropertyValue(array $property_criteria);

    /**
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder
     * @return Entity|null
     */
    function getOneByCriteria(\Everon\DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder = null);

    /**
     * @return \Everon\Interfaces\DataMapper
     */
    function getMapper();

    /**
     * @param \Everon\Interfaces\DataMapper $Mapper
     */
    function setMapper(\Everon\Interfaces\DataMapper $Mapper);

    function getName();

    /**
     * Creates NEW entity from array
     * 
     * @param array $data
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity
     */
    function createFromArray(array $data, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria=null);

    /**
     * * Creates EXISTING entity from array
     * 
     * @param array $data
     * @param \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity
     */
    function buildFromArray(array $data, \Everon\DataMapper\Interfaces\Criteria\Builder $RelationCriteria=null);

    /**
     * @param Entity $Entity
     * @param array $data
     */
    function updateFromArray(Entity $Entity, array $data);

    /**
     * @param $point null
     */
    function beginTransaction($point=null);

    /**
     * @param $point null
     */
    function commitTransaction($point=null);

    /**
     * @param $point null
     */
    function rollbackTransaction($point=null);
}
