<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

use Everon\DataMapper\Interfaces\Criteria;
use Everon\Dependency;
use Everon\Domain;
use Everon\Domain\Interfaces;
use Everon\Exception;
use Everon\Interfaces\Collection;
use Everon\Interfaces\DataMapper;
use Everon\Helper;

abstract class Repository implements Interfaces\Repository
{
    use Domain\Dependency\Injection\DomainManager;
    use Dependency\Injection\Factory;
    use Dependency\Injection\Logger;
    use Helper\Arrays;
    use Helper\Asserts\IsArrayKey;
    
    /**
     * @var DataMapper
     */
    protected $Mapper = null;
    
    protected $name = null;

    /**
     * @var Collection
     */
    protected $RelationCollection = null;


    /**
     * @param $name
     * @param DataMapper $Mapper
     */
    public function __construct($name, DataMapper $Mapper)
    {
        $this->name = $name;
        $this->Mapper = $Mapper;
    }

    /**
     * Makes sure data defined in the Entity is in proper format
     *
     * @param array $data
     * @return array
     * @throws \Everon\Exception\Domain
     */
    protected function prepareDataForEntity(array $data)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Schema\Column $Column
         */
        foreach ($data as $name => $value) {
            if ($this->getMapper()->getTable()->hasColumn($name)) {
                if (array_key_exists($name, $data) === false) {
                    throw new Exception\Domain('Missing Entity data: "%s" for "%s"', [$name, $this->getMapper()->getTable()->getName()]);
                }
                $Column = $this->getMapper()->getTable()->getColumnByName($name);
                $data[$name] = $Column->getColumnDataForEntity($data[$name]);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param Criteria $RelationCriteria
     * @return Interfaces\Entity
     */
    protected function buildEntity(array $data, Criteria $RelationCriteria=null)
    {
        $defaults = $this->getDefaultColumns();
        $data = $this->arrayMergeDefault($defaults, $data);
        $data = $this->prepareDataForEntity($data);
        $Entity = $this->getFactory()->buildDomainEntity($this->getName(), $this->getMapper()->getTable()->getPk(), $data);
        $this->buildEntityRelations($Entity, $RelationCriteria);
        //$this->resolveRelationsIntoData($Entity);
        return $Entity;
    }

    /**
     * @return array
     */
    protected function getDefaultColumns()
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Schema\Column $Column
         */
        $default = [];
        $columns = $this->getMapper()->getTable()->getColumns();
        foreach ($columns as $Column) {
            if ($Column->isPk()) {
                continue;
            }
            $default[$Column->getName()] = null;
        }
        
        return $default;
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param Criteria $Criteria
     */
    public function buildEntityRelations(Interfaces\Entity $Entity, Criteria $Criteria = null)
    {
        $Criteria = $Criteria ?: (new \Everon\DataMapper\CriteriaOLD())->limit(20)->offset(0);
        
        $RelationCollection = new Helper\Collection([]);
        //buildDomainRelationMapper
        foreach ($Entity->getRelationDefinition() as $relation_domain_name => $relation_data) {
            $relation_data = $this->arrayMergeDefault([
                'type' => null,
                'column' => null,
                'mapped_by' => null,
                'inversed_by' => null,
                'virtual' => false,
            ], $relation_data);
            
            $RelationMapper = $this->getFactory()->buildDomainRelationMapper(
                $relation_data['type'], $relation_domain_name, $relation_data['column'], $relation_data['mapped_by'], $relation_data['inversed_by'], $relation_data['virtual']
            );

            $Relation = $this->getFactory()->buildDomainRelation($relation_domain_name, $Entity, $RelationMapper);
            $Relation->setCriteria(clone $Criteria);
            $Relation->setEntityRelationCriteria(clone $Criteria);
            $RelationCollection->set($relation_domain_name, $Relation);
        }

        $Entity->setRelationCollection($RelationCollection);
    }

    /**
     * @param Interfaces\Entity $Entity
     * @throws \Everon\Exception\Domain
     */
    protected function AAAresolveRelationsIntoData(Interfaces\Entity $Entity)
    {
        if ($Entity->isNew()) {
            return;
        }

        foreach ($Entity->getRelationCollection() as $domain_name => $Relation) {
            $Relation->resolveRelationsIntoData($Entity);
        }

        return;
        
        /**
         * @var \Everon\Domain\Interfaces\Relation $Relation
         */
        foreach ($Entity->getRelationCollection() as $domain_name => $Relation) {
            if ($Relation->getRelationMapper()->isOwningSide() === false && $Relation->getRelationMapper()->isVirtual() === false) {
                switch ($Relation->getType()) {
                    case Domain\Relation::ONE_TO_ONE:
                        //d($Entity->getId(), $Entity->getDomainName(), $Relation->getRelationMapper()->getColumn());
                        $value = $Entity->getValueByName($Relation->getRelationMapper()->getColumn());
                        $Column = $this->getMapper()->getTable()->getColumnByName($Relation->getRelationMapper()->getColumn());
                        break;

                    default:
                        $value = $Entity->getValueByName($Relation->getRelationMapper()->getMappedBy());
                        $Column = $this->getMapper()->getTable()->getColumnByName($Relation->getRelationMapper()->getMappedBy());
                        break;
                }

                if ($Column->isPk() && $Entity->isNew() && $value === null) {
                    continue;
                }


                if ($Column->isNullable() && $value === null) {
                    $Entity->getRelationByName($Relation->getName())->reset();
                    $Entity->setValueByName($Relation->getRelationMapper()->getMappedBy(), null);
                    continue;
                }

                
                $ChildEntity = $this->getDomainManager()->getRepositoryByName($Relation->getName())->getEntityByPropertyValue([
                    $Relation->getRelationMapper()->getInversedBy() => $value
                ]);
                
                if ($ChildEntity === null) {
                    //throw new Exception\Domain('RelationEntity: "%s" could not be resolved for "%s@%s" with value "%s"', [$Entity->getDomainName(), $Relation->getName(), $Relation->getRelationMapper()->getInversedBy(), $value]);
                    $Entity->getRelationByName($Relation->getName())->reset();
                }
                else {
                    $Entity->getRelationByName($Relation->getName())->setOne($ChildEntity); //update relation
                    $Entity->setValueByName($Relation->getRelationMapper()->getInversedBy(), $ChildEntity->getValueByName($Relation->getRelationMapper()->getInversedBy())); //update fields represented in relations eg. user_id -> User->getId()
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getMapper()
    {
        return $this->Mapper;
    }

    /**
     * @inheritdoc
     */
    public function setMapper(DataMapper $Mapper)
    {
        $this->Mapper = $Mapper;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function buildFromArray(array $data, Criteria $RelationCriteria=null)
    {
        return $this->buildEntity($data, $RelationCriteria);
    }

    /**
     * @inheritdoc
     */
    public function persistFromArray(array $data, $user_id=null)
    {
        $Entity = $this->buildFromArray($data);
        $this->persist($Entity, $user_id);
        return $Entity;
    }

    /**
     * @inheritdoc
     */
    public function validateEntity(Interfaces\Entity $Entity)
    {
        return $this->getMapper()->getTable()->prepareDataForSql($Entity->toArray(), $Entity->isNew() === false);
    }

    /**
     * @inheritdoc
     */
    public function getEntityById($id, Criteria $RelationCriteria=null)
    {
        $Criteria = (new \Everon\DataMapper\CriteriaOLD())->where([
            $this->getMapper()->getTable()->getPk() => $id
        ]);
        return $this->getOneByCriteria($Criteria, $RelationCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getEntityByPropertyValue(array $property_criteria, Criteria $RelationCriteria=null)
    {
        if (empty($property_criteria)) {
            return null;
        }
        $Criteria = (new \Everon\DataMapper\CriteriaOLD())->where($property_criteria);
        return $this->getOneByCriteria($Criteria, $RelationCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getOneByCriteria(Criteria $Criteria, Criteria $RelationCriteria=null)
    {
        $Criteria->limit(1);
        $Criteria->offset(0);
        $data = $this->getMapper()->fetchOneByCriteria($Criteria);
        if (empty($data)) {
            return null;
        }

        return $this->buildEntity($data, $RelationCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getByCriteria(Criteria $Criteria, Criteria $RelationCriteria=null)
    {
        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            return null;
        }
        
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->buildEntity($item, $RelationCriteria);
        }
        
        return $result;
    }
    
    /**
     * @inheritdoc
     */
    public function count(Criteria $Criteria=null)
    {
        return $this->getMapper()->count($Criteria);
    }

    /**
     * @inheritdoc
     */
    public function persist(Interfaces\Entity $Entity, $user_id=null)
    {
        if ($Entity->isDeleted()) {
            throw new \Everon\Exception\Domain('Invalid entity state when attempting to persist entity: "%s@%s"', [$Entity->getDomainName(), $Entity->getId()]);
        }
        
        $this->validateEntity($Entity);
        
        $data = $Entity->toArray();
        if ($Entity->isNew()) {
            $data = $this->getMapper()->add($data, $user_id);
        }
        else {
            $this->getMapper()->save($data, $user_id);
        }

        $Entity->persist($data);
        //$this->resolveRelationsIntoData($Entity);
    }

    /**
     * @inheritdoc
     */
    public function remove(Interfaces\Entity $Entity, $user_id=null)
    {
        if ($Entity->isNew() || $Entity->isDeleted()) {
            throw new \Everon\Exception\Domain('Invalid entity state when attempting to delete entity: "%s@%s"', [$Entity->getDomainName(), $Entity->getId()]);
        }
        
        $this->getMapper()->delete($Entity->getId(), $user_id);
        $Entity->delete();
    }

    /**
     * @inheritdoc
     */
    public function removeByCriteria(Criteria $Criteria)
    {
        $this->getMapper()->deleteByCriteria($Criteria);
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction($point=null)
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->beginTransaction();
        if ($point !== null) {
            $this->getLogger()->transaction('begin: '.$point);
        }
        else {
            $this->getLogger()->transaction('begin');
        }
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction($point=null)
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->commitTransaction();
        if ($point !== null) {
            $this->getLogger()->transaction('commit: '.$point);
        }
        else {
            $this->getLogger()->transaction('commit');
        }
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction($point=null)
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->rollbackTransaction();
        if ($point !== null) {
            $this->getLogger()->transaction('rollback: '.$point);
        }
        else {
            $this->getLogger()->transaction('rollback');
        }
    }
}