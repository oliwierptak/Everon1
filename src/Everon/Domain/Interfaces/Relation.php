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
     * @return DataMapper\Interfaces\Criteria\Builder
     */
     function getCriteriaBuilder();

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return mixed
     */
     function setCriteriaBuilder(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder);

    /**
     * @return DataMapper\Interfaces\Criteria\Builder
     */
     function getEntityRelationCriteria();

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder
     * @return mixed
     */
     function setEntityRelationCriteria(DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder);

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
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return \Everon\Interfaces\Collection
     */
    function getData(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder = null);

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return int
     */
    function getCount(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder=null);

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
    function getMany($CriteriaBuilder = null);

    function reset();
}