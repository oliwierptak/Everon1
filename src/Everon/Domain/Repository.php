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
use Everon\Domain\Interfaces;
use Everon\Interfaces\Collection;
use Everon\Interfaces\DataMapper;
use Everon\Helper;

abstract class Repository implements Interfaces\Repository
{
    use Dependency\Injection\DomainManager;
    use Dependency\Injection\Factory;
    
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
     * @param Interfaces\Entity $Entity
     * @param Criteria $Criteria
     * @return mixed
     */
    abstract public function buildEntityRelations(Interfaces\Entity $Entity, Criteria $Criteria);


    /**
     * @param $name
     * @param DataMapper $Mapper
     */
    public function __construct($name, DataMapper $Mapper)
    {
        $this->name = $name;
        $this->Mapper = $Mapper;
    }
    
    protected function buildRelations(Interfaces\Entity $Entity, Criteria $RelationCriteria=null)
    {
        $RelationCriteria = $RelationCriteria ?: (new \Everon\DataMapper\Criteria())->limit(10)->offset(0);
        $this->buildEntityRelations($Entity, $RelationCriteria);
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
    public function addFromArray(array $data, Criteria $RelationCriteria=null)
    {
        $Entity = $this->buildEntity($data, $RelationCriteria, true);
        $this->persist($Entity);
        return $Entity;
    }
    
    /**
     * @inheritdoc
     */
    public function saveFromArray(array $data, Criteria $RelationCriteria=null)
    {
        $Entity = $this->buildEntity($data, $RelationCriteria);
        $this->persist($Entity);
        return $Entity;
    }
    
    /**
     * @inheritdoc
     */
    public function buildEntity(array $data, Criteria $RelationCriteria=null, $is_new=false)
    {
        $id = ($is_new === false) ? $this->getMapper()->getAndValidateId($data) : null;
        $data[$this->getMapper()->getTable()->getPk()] = $id;

        $Entity = $this->getFactory()->buildDomainEntity($this->getName(), $id, $data);
        $this->buildRelations($Entity, $RelationCriteria);

        return $Entity;
    }
    
    /**
     * @param $id
     * @param Criteria $RelationCriteria
     * @return Interfaces\Entity|null
     */
    public function getEntityById($id, Criteria $RelationCriteria=null)
    {
        $Criteria = (new \Everon\DataMapper\Criteria())->where([
            $this->getMapper()->getTable()->getPk() => $id
        ]);
        
        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            return null;
        }

        $data = current($data);
        return $this->buildEntity($data, $RelationCriteria);
    }

    /**
     * @param Criteria $Criteria
     * @param Criteria $RelationCriteria
     * @return array|null
     */
    public function getList(Criteria $Criteria, Criteria $RelationCriteria=null)
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
    public function persist(Interfaces\Entity $Entity)
    {
        if ($Entity->isNew()) {
            $id = $this->getMapper()->add($Entity);
        }
        else {
            $id = $Entity->getId();
            $this->getMapper()->save($Entity);
        }

        $data = $Entity->toArray();
        $Entity->persist($id, $data);
    }

    /**
     * @inheritdoc
     */
    public function remove(Interfaces\Entity $Entity)
    {
        $this->getMapper()->delete($Entity);
        $Entity->delete();
    }
}
