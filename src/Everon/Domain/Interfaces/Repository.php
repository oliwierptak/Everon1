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

use Everon\DataMapper\Interfaces\Criteria;
use Everon\Interfaces\DataMapper;
use Everon\Domain\Exception;

interface Repository
{
    /**
     * @param Entity $Entity
     * @param Criteria $Criteria
     * @return void
     */
    function buildEntityRelations(Entity $Entity, Criteria $Criteria);
    
    /**
     * @param $id
     * @return Entity
     * @throws Exception\Repository
     */
    function getEntityById($id);

    /**
     * @param array $property_criteria
     * @param Criteria $RelationCriteria
     * @return Entity|null
     */
    function getEntityByPropertyValue(array $property_criteria, Criteria $RelationCriteria=null);

    /**
     * @param array $data
     * @param int $user_id
     * @return mixed
     */
    function persistFromArray(array $data, $user_id=null);

    /**
     * @param Criteria $Criteria
     * @return array|null
     */
    function getByCriteria(Criteria $Criteria);

    /**
     * @param Criteria $Criteria
     * @return int
     */
    function count(Criteria $Criteria=null);
        
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
     * @param Criteria $Criteria
     * @param Criteria $RelationCriteria
     * @return Entity|null
     */
    function getOneByCriteria(Criteria $Criteria, Criteria $RelationCriteria=null);

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

    function beginTransaction();

    function commitTransaction();

    function rollbackTransaction();
}
