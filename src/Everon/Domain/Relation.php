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

use Everon\DataMapper;
use Everon\Domain;
use Everon\Helper;

abstract class Relation implements Interfaces\Relation
{
    use Domain\Dependency\Injection\DomainManager;
    use Helper\Arrays;
    use Helper\String\LastTokenToName;
    use Helper\ToArray;

    const ONE_TO_ONE = 'OneToOne';
    const ONE_TO_MANY = 'OneToMany';
    const MANY_TO_MANY = 'ManyToMany';
    const MANY_TO_ONE = 'ManyToOne';

    protected $type = self::ONE_TO_ONE;

    /**
     * @var string
     */
    protected $sql = null;

    /**
     * @var string
     */
    protected $sql_count = null;

    /**
     * @var DataMapper\Interfaces\CriteriaOLD
     */
    protected $Criteria = null;

    /**
     * @var DataMapper\Interfaces\CriteriaOLD
     */
    protected $EntityRelationCriteria = null;

    /**
     * @var Domain\Interfaces\Repository
     */
    protected $OwnerEntity = null;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $Data = null;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var Domain\Interfaces\RelationMapper
     */
    protected $RelationMapper = null;

    
    /**
     * @param Interfaces\Entity $OwnerEntity
     * @param Interfaces\RelationMapper $RelationMapper
     */
    public function __construct(Domain\Interfaces\Entity $OwnerEntity, Domain\Interfaces\RelationMapper $RelationMapper)
    {
        $this->OwnerEntity = $OwnerEntity;
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->Data = new Helper\Collection([]);
        $this->RelationMapper = $RelationMapper;
        $this->Criteria = (new \Everon\DataMapper\CriteriaOLD())->limit(10)->offset(0);
    }
    
    protected function setupRelationParameters()
    {
        if ($this->getRelationMapper()->isVirtual()) { //virtual relations handle their data on their own
            return;
        }

        $this->validate();
        $this->setupRelation();
    }
    
    protected function validate()
    {
        
    }
    
    protected function setupRelation()
    {
        $domain_name = $this->getOwnerEntity()->getDomainName();
        $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
        
        if (array_key_exists($this->getRelationMapper()->getMappedBy(), $Repository->getMapper()->getTable()->getForeignKeys()) === false) {
            throw new \Everon\Exception\Domain('Invalid relation "mapped_by" for: "%s@%s', [$this->getName(), $this->getRelationMapper()->getMappedBy()]);
        }

        /**
         * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
         */
        $ForeignKey = $Repository->getMapper()->getTable()->getForeignKeys()[$this->getRelationMapper()->getMappedBy()];
        $table = $ForeignKey->getForeignFullTableName();
        $target_column = $this->getRelationMapper()->getInversedBy() ?: $ForeignKey->getForeignColumnName();

        $value = $this->getOwnerEntity()->getValueByName($this->getRelationMapper()->getMappedBy());
        $Column = $Repository->getMapper()->getTable()->getColumnByName($this->getRelationMapper()->getMappedBy()); //todo DataMapper leak
        if ($Column->isNullable() && $value === null) {
            $this->loaded = true; //xxx the value is null stop loading
            return;
        }

        $this->getCriteria()->where([
            't.'.$target_column => $this->getDataMapper()->getSchema()->getTableByName($table)->validateId($value)
        ]);
    }

    /**
     * @throws \Everon\Exception\Domain
     */
    public function AAresolveRelationsIntoData(Interfaces\Entity $Entity)
    {
        die('implment me: '.$this->getName());
        if ($this->getRelationMapper()->isOwningSide() || $this->getRelationMapper()->isVirtual()) {
            return;
        }
        
        $value = $this->getOwnerEntity()->getValueByName($this->getRelationMapper()->getMappedBy());
        $Column = $this->getDataMapper()->getTable()->getColumnByName($this->getRelationMapper()->getMappedBy());

        if ($Column->isPk() && $this->getOwnerEntity()->isNew() && $value === null) {
            return;
        }

        if ($Column->isNullable() && $value === null) {
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
            $this->getOwnerEntity()->setValueByName($this->getRelationMapper()->getMappedBy(), null);
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
            return;
        }

        $ChildEntity = $this->getDomainManager()->getRepositoryByName($this->getName())->getEntityByPropertyValue([
            $this->getRelationMapper()->getInversedBy() => $value
        ]);

        if ($ChildEntity === null) {
            //throw new Exception\Domain('RelationEntity: "%s" could not be resolved for "%s@%s" with value "%s"', [$this->getOwnerEntity()->getDomainName(), $this->getName(), $this->getRelationMapper()->getInversedBy(), $value]);
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
        }
        else {
            $this->getOwnerEntity()->getRelationByName($this->getName())->setOne($ChildEntity); //update relation
            $this->getOwnerEntity()->setValueByName($this->getRelationMapper()->getMappedBy(), $ChildEntity->getValueByName($this->getRelationMapper()->getMappedBy())); //update fields represented in relations eg. user_id -> User->getId()
        }
    }
    
    protected function resetRelationCriteriaParameters()
    {
        $this->setCriteria(new \Everon\DataMapper\CriteriaOLD());
        $this->sql = null;
        $this->sql_count = null;
    }

    public function reset()
    {
        $this->loaded = false;
    }

    /**
     * @return array
     */
    protected function getToArray($deep=false)
    {
        return $this->getData()->toArray($deep);
    }

    /**
     * @return Domain\Interfaces\Entity
     */
    public function getOwnerEntity()
    {
        return $this->OwnerEntity;
    }

    /**
     * @param Domain\Interfaces\Entity $Entity
     */
    public function setOwnerEntity(Domain\Interfaces\Entity $Entity)
    {
        $this->OwnerEntity = $Entity;
        $this->reset();
    }

    /**
     * @return DataMapper\Interfaces\CriteriaOLD
     */
    public function getCriteria()
    {
        return $this->Criteria;
    }

    /**
     * @param DataMapper\Interfaces\CriteriaOLD $Criteria
     */
    public function setCriteria(DataMapper\Interfaces\CriteriaOLD $Criteria)
    {
        $this->Criteria = $Criteria;
        $this->reset();
    }

    /**
     * @return DataMapper\Interfaces\CriteriaOLD
     */
    public function getEntityRelationCriteria()
    {
        return $this->EntityRelationCriteria;
    }

    /**
     * @param DataMapper\Interfaces\CriteriaOLD $RelationCriteria
     */
    public function setEntityRelationCriteria(DataMapper\Interfaces\CriteriaOLD $RelationCriteria)
    {
        $this->EntityRelationCriteria = $RelationCriteria;
        $this->reset();
    }

    /**
     * @return \Everon\Interfaces\DataMapper
     */
    public function getDataMapper()
    {
        return $this->getDomainManager()->getRepositoryByName($this->getName())->getMapper();
    }

    /**
     * @param \Everon\Interfaces\DataMapper $DataMapper
     */
    public function setDataMapper(\Everon\Interfaces\DataMapper $DataMapper)
    {
        $this->getDomainManager()->getRepositoryByName($this->getName())->setMapper($DataMapper);
        $this->reset();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->reset();
    }

    /**
     * @return Domain\Interfaces\Repository
     */
    public function getRepository()
    {
        return $this->getDomainManager()->getRepositoryByName($this->getName());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->reset();
    }

    /**
     * @param \Everon\Domain\Interfaces\RelationMapper $RelationMapper
     */
    public function setRelationMapper(Domain\Interfaces\RelationMapper $RelationMapper)
    {
        $this->RelationMapper = $RelationMapper;
    }

    /**
     * @return \Everon\Domain\Interfaces\RelationMapper
     */
    public function getRelationMapper()
    {
        return $this->RelationMapper;
    }
    
    /**
     * @param \Everon\Interfaces\Collection $Collection
     */
    public function setData(\Everon\Interfaces\Collection $Collection)
    {
        $this->Data = $Collection;
        $this->loaded = true;
    }

    /**
     * @inheritdoc
     */
    public function getData(DataMapper\Interfaces\CriteriaOLD $Criteria=null)
    {
        if ($this->loaded) {
            return $this->Data;
        }

        $this->setupRelationParameters();
        
        if ($Criteria !== null) { //todo: woah refactor
            $shit = $this->getCriteria()->toArray();
            $where = $this->arrayMergeDefault($shit['where'], $Criteria->getWhere());
            $NewCriteria = new \Everon\DataMapper\CriteriaOLD();
            $NewCriteria->where($where);
            $NewCriteria->orderBy($this->getCriteria()->getOrderBy() ?: $Criteria->getOrderBy());
            $NewCriteria->limit($this->getCriteria()->getLimit() ?: $Criteria->getLimit());
            $NewCriteria->offset($this->getCriteria()->getOffset() ?: $Criteria->getOffset());
            $this->setCriteria($NewCriteria);
        }

        $Loader = function () {
            if ($this->sql !== null) {
                $sql = $this->sql.$this->getCriteria();
                $data = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $this->getCriteria()->getWhere())->fetchAll();
            } 
            else {
                $data = $this->getDataMapper()->fetchAll($this->getCriteria());
            }

            if ($data === false || empty($data)) {
                return [];
            }

            $entities = [];
            foreach ($data as $item) {
                $entities[] = $this->getRepository()->buildFromArray($item, $this->getEntityRelationCriteria());
            }

            return $entities;
        };

        $this->Data = new Helper\LazyCollection($Loader);
        $this->loaded = true;

        return $this->Data;
    }

    public function getCount()
    {
        $this->setupRelationParameters();

        if ($this->loaded === false) {
            $Criteria = clone $this->getCriteria();
            $Criteria->orderBy([]);
            $Criteria->limit(0);
            $Criteria->offset(0);
            
            if ($this->sql_count !== null) {
                $sql = $this->sql_count.' '.$Criteria;
                $PdoStatement = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $Criteria->getWhere());
                $this->count = (int) $PdoStatement->fetchColumn();
            } 
            else {
                return $this->getDomainManager()->getRepositoryByName($this->getName())->count($Criteria);
            }
        }

        return $this->count;
    }

    public function setOne(Domain\Interfaces\Entity $Entity)
    {
        $this->loaded = true;
        $this->Data = new Helper\Collection([$Entity]);
    }

    /**
     * @return Domain\Interfaces\Entity
     */
    public function getOne()
    {
        if ($this->getData()->isEmpty()) {
            return null;
        }
        
        $data = $this->getData()->toArray();
        if (empty($data)) {
            return null;
        }
        
        if (is_array($data)) {
            return current($data);
        }
        
        return null;
    }

    /**
     * @param array $data
     */
    public function setMany(array $data)
    {
        $this->loaded = true;
        $this->Data = new Helper\Collection($data);
    }

    /**
     * @return array
     */
    public function getMany()
    {
        return $this->getData()->toArray();
    }

}