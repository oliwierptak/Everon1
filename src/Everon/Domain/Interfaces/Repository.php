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

use Everon\DataMapper;
use Everon\Domain\Exception;

interface Repository
{
    /**
     * @param Entity $Entity
     * @return array
     */
    function validateEntity(Entity $Entity);

    /**
     * @param Entity $Entity
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @internal param CriteriaOLD $CriteriaBuilder
     * @return
     */
    function buildEntityRelations(Entity $Entity, DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder = null);

    /**
     * @param $id
     * @param  DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity|null
     */
    function getEntityById($id, DataMapper\Interfaces\Criteria\Builder $RelationCriteria=null);

    /**
     * @param array $property_criteria
     * @param DataMapper\Interfaces\Criteria\Builder|CriteriaOLD $RelationCriteria
     * @return Entity|null
     */
    function getEntityByPropertyValue(array $property_criteria, DataMapper\Interfaces\Criteria\Builder $RelationCriteria = null);

    /**
     * @param array $data
     * @param int $user_id
     * @return mixed
     */
    function persistFromArray(array $data, $user_id=null);

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @param DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder
     * @return array|null
     */
    function getByCriteria(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder, DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder=null);

    /**
     * @param DataMapper\Interfaces\Criteria\Builder|CriteriaOLD $CriteriaBuilder
     * @return int
     */
    function count(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder = null);

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
     * @param DataMapper\Interfaces\Criteria\Builder|CriteriaOLD $CriteriaBuilder
     * @return
     */
    function removeByCriteria(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @param DataMapper\Interfaces\Criteria\Builder|CriteriaOLD $CriteriaBuilder
     * @param DataMapper\Interfaces\Criteria\Builder|CriteriaOLD $RelationCriteriaBuilder
     * @return Entity|null
     */
    function getOneByCriteria(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder, DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder = null);

    /**
     * @return DataMapper
     */
    function getMapper();

    /**
     * @param DataMapper $Mapper
     */
    function setMapper(DataMapper $Mapper);

    function getName();

    /**
     * @param array $data
     * @param DataMapper\Interfaces\Criteria\Builder $RelationCriteria
     * @return Entity
     */
    function buildFromArray(array $data, DataMapper\Interfaces\Criteria\Builder $RelationCriteria=null);

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
