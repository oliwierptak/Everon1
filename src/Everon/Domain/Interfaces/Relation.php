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
use Everon\Domain;

interface Relation extends \Everon\Interfaces\Arrayable, Dependency\DomainManager
{
    /**
     * @return Domain\Interfaces\Entity
     */
     function getOwnerEntity();

    /**
     * @param Domain\Interfaces\Entity $Entity
     */
     function setOwnerEntity(Domain\Interfaces\Entity $Entity);

    /**
     * @return DataMapper\Interfaces\CriteriaOLD
     */
     function getCriteria();

    /**
     * @param DataMapper\Interfaces\CriteriaOLD $Criteria
     */
     function setCriteria(DataMapper\Interfaces\CriteriaOLD $Criteria);

    /**
     * @return DataMapper\Interfaces\CriteriaOLD
     */
     function getEntityRelationCriteria();

    /**
     * @param DataMapper\Interfaces\CriteriaOLD $RelationCriteria
     */
     function setEntityRelationCriteria(DataMapper\Interfaces\CriteriaOLD $RelationCriteria);

    /**
     * @return \Everon\Interfaces\DataMapper
     */
     function getDataMapper();

    /**
     * @param \Everon\Interfaces\DataMapper $DataMapper
     */
     function setDataMapper(\Everon\Interfaces\DataMapper $DataMapper);

    /**
     * @return string
     */
     function getName();

    /**
     * @param string $name
     */
     function setName($name);

    /**
     * @return Domain\Interfaces\Repository
     */
     function getRepository();

    /**
     * @return string
     */
    function getType();

    /**
     * @param string $type
     */
    function setType($type);

    /**
     * @param \Everon\Domain\Interfaces\RelationMapper $RelationMapper
     */
    function setRelationMapper(Domain\Interfaces\RelationMapper $RelationMapper);
        
    /**
     * @return \Everon\Domain\Interfaces\RelationMapper
     */
    function getRelationMapper();

    /**
     * @param \Everon\Interfaces\Collection $Collection
     */
    function setData(\Everon\Interfaces\Collection $Collection);

    /**
     * @param DataMapper\Interfaces\CriteriaOLD $Criteria 
     * @return \Everon\Interfaces\Collection
     */
    function getData(DataMapper\Interfaces\CriteriaOLD $Criteria=null);

    function getCount();

    /**
     * @param Entity $Entity
     */
    function setOne(Domain\Interfaces\Entity $Entity);

    /**
     * @return Entity
     */
    function getOne();

    /**
     * @param array $data
     */
    function setMany(array $data);

    /**
     * @return array
     */
    function getMany();

    function reset();
}