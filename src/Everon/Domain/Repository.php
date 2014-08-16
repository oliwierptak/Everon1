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
     * @param Interfaces\Entity $Entity
     * @param Criteria $RelationCriteria
     */
    protected function buildRelations(Interfaces\Entity $Entity, Criteria $RelationCriteria=null)
    {
        $RelationCriteria = $RelationCriteria ?: (new \Everon\DataMapper\Criteria())->limit(20)->offset(0);
        $this->buildEntityRelations($Entity, $RelationCriteria);
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
                $data[$name] = $Column->getDataForEntity($data[$name]);
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
        $data = $this->prepareDataForEntity($data);
        $Entity = $this->getFactory()->buildDomainEntity($this->getName(), $this->getMapper()->getTable()->getPk(), $data);
        $this->buildRelations($Entity, $RelationCriteria);
        return $Entity;
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param Criteria $Criteria
     * @throws \Everon\Exception\Domain
     */
    public function buildEntityRelations(Interfaces\Entity $Entity, Criteria $Criteria)
    {
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
                $relation_data['type'], 
                $relation_domain_name, 
                $relation_data['column'], 
                $relation_data['mapped_by'], 
                $relation_data['inversed_by'], 
                $relation_data['virtual']
            );

            $Relation = $this->getFactory()->buildDomainRelation($relation_domain_name, $this->getName(), $Entity, $RelationMapper);
            
            if ($RelationMapper->isVirtual() === false) {//virtual relations handle their data on their own
                if (array_key_exists($RelationMapper->getMappedBy(), $this->getMapper()->getTable()->getForeignKeys()) === false) {
                    throw new Exception\Domain('Invalid relation mapping for: "%s@%s', [$relation_domain_name, $RelationMapper->getMappedBy()]);
                }

                /**
                 * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
                 */
                $ForeignKey = $this->getMapper()->getTable()->getForeignKeys()[$RelationMapper->getMappedBy()];
                $table = $ForeignKey->getForeignFullTableName();
                $target_column = $RelationMapper->getColumn() ?: $ForeignKey->getForeignColumnName();

                $RelationCriteria = clone $Criteria;
                $RelationCriteria->where([
                    't.'.$target_column => $this->getMapper()->getSchema()->getTableByName($table)->validateId($Entity->getValueByName($RelationMapper->getMappedBy()))
                ]);

                $Relation->setCriteria($RelationCriteria);
            }
            
            $Relation->setEntityRelationCriteria(clone $Criteria);
            $RelationCollection->set($relation_domain_name, $Relation);
        }

        $Entity->setRelationCollection($RelationCollection);
    }

    protected function setupRelations(Interfaces\Entity $Entity)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
         */
        $domain_name = $this->getDomainManager()->getDataMapperManager()->getDomainMapper()->getDomainName($this->getMapper()->getTable()->getOriginalName());
        //$foreign_keys = $Repository->getMapper()->getTable()->getForeignKeyByTableName($Repository->getMapper()->getTable()->getOriginalName());
        $foreign_keys = $this->getMapper()->getTable()->getForeignKeys();
        dd($domain_name, $foreign_keys, $this->getMapper()->getTable()->getOriginalName(), $this->getMapper()->getTable()->getName());
        foreach ($foreign_keys as $column_name => $ForeignKey) {
            $column = $ForeignKey->getColumnName();
            $table = $ForeignKey->getForeignFullTableName();
            $id_field = $ForeignKey->getForeignColumnName();

            $this->getCriteria()->where([
                't.'.$column => $this->getDataMapper()->getSchema()->getTableByName($table)->validateId($Entity->getValueByName($id_field))
            ]);
        }

        dd($this->getCriteria()->getWhere());
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
        $data = $Entity->toArray();
        return $this->getMapper()->getTable()->prepareDataForSql($data, $Entity->isNew() === false);
    }

    /**
     * @inheritdoc
     */
    public function getEntityById($id, Criteria $RelationCriteria=null)
    {
        $Criteria = (new \Everon\DataMapper\Criteria())->where([
            $this->getMapper()->getTable()->getPk() => $id
        ]);
        return $this->getOneByCriteria($Criteria, $RelationCriteria);
    }

    /**
     * @inheritdoc
     */
    public function getEntityByPropertyValue(array $property_criteria, Criteria $RelationCriteria=null)
    {
        $Criteria = (new \Everon\DataMapper\Criteria())->where($property_criteria);
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
        $data = $Entity->toArray();
        if ($Entity->isNew()) {
            $data = $this->getMapper()->add($data, $user_id);
        }
        else {
            $this->getMapper()->save($data, $user_id);
        }

        $Entity->persist($data);
    }

    /**
     * @inheritdoc
     */
    public function remove(Interfaces\Entity $Entity, $user_id=null)
    {
        $this->getMapper()->delete($Entity->getId(), $user_id);
        $Entity->delete();
    }

    public function beginTransaction()
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->commitTransaction();
    }

    public function rollbackTransaction()
    {
        $this->getMapper()->getSchema()->getPdoAdapterByName($this->getMapper()->getWriteConnectionName())->rollbackTransaction();
    }
}