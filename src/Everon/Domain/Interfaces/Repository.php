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

use Everon\DataMapper\Interfaces\CriteriaOLD;
use Everon\Interfaces\DataMapper;
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
     * @param CriteriaOLD $Criteria
     * @return
     */
    function buildEntityRelations(Entity $Entity, CriteriaOLD $Criteria = null);

    /**
     * @param $id
     * @param CriteriaOLD $RelationCriteria
     * @return Entity|null
     */
    function getEntityById($id, CriteriaOLD $RelationCriteria=null);

    /**
     * @param array $property_criteria
     * @param CriteriaOLD $RelationCriteria
     * @return Entity|null
     */
    function getEntityByPropertyValue(array $property_criteria, CriteriaOLD $RelationCriteria=null);

    /**
     * @param array $data
     * @param int $user_id
     * @return mixed
     */
    function persistFromArray(array $data, $user_id=null);

    /**
     * @param CriteriaOLD $Criteria
     * @return array|null
     */
    function getByCriteria(CriteriaOLD $Criteria);

    /**
     * @param CriteriaOLD $Criteria
     * @return int
     */
    function count(CriteriaOLD $Criteria=null);

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
     * @param CriteriaOLD $Criteria
     */
    function removeByCriteria(CriteriaOLD $Criteria);

    /**
     * @param CriteriaOLD $Criteria
     * @param CriteriaOLD $RelationCriteria
     * @return Entity|null
     */
    function getOneByCriteria(CriteriaOLD $Criteria, CriteriaOLD $RelationCriteria=null);

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
     * @return Entity
     */
    function buildFromArray(array $data);

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
