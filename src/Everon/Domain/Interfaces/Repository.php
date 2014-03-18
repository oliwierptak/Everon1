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
     * @return mixed
     */
    function buildEntityRelations(Entity $Entity, Criteria $Criteria);
    
    /**
     * @param $id
     * @return Entity
     * @throws Exception\Repository
     */
    function getEntityById($id);

    /**
     * @param array $data
     * @return mixed
     */
    function persistFromArray(array $data);

    /**
     * @param Criteria $Criteria
     * @return array|null
     */
    function getList(Criteria $Criteria);
        
    /**
     * @param Entity $Entity
     */
    function persist(Entity $Entity);

    /**
     * @param Entity $Entity
     */
    function remove(Entity $Entity);

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
}
