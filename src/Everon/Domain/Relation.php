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
use Everon\Dependency;
use Everon\Domain;
use Everon\Helper;

abstract class Relation implements Interfaces\Relation
{
    use Dependency\Injection\Factory;
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
     * @var DataMapper\Interfaces\Criteria\Builder
     */
    protected $CriteriaBuilder = null;

    /**
     * @var DataMapper\Interfaces\Criteria\Builder
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
    
    protected $relation_was_setup = false;

    
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
    }
    
    protected function setupRelationParameters()
    {
        $this->resetRelationCriteriaParameters();
        
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

        $this->getCriteriaBuilder()->where('t.'.$target_column, '=', $this->getDataMapper()->getSchema()->getTableByName($table)->validateId($value));
    }

    protected function resetRelationCriteriaParameters()
    {
        $CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        $this->CriteriaBuilder = $CriteriaBuilder;
        $this->sql = null;
        $this->sql_count = null;
    }

    public function reset()
    {
        $this->loaded = false;
        $this->resetRelationCriteriaParameters();
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
    }

    /**
     * @return DataMapper\Interfaces\Criteria\Builder
     */
    public function getCriteriaBuilder()
    {
        if ($this->CriteriaBuilder === null) {
            $this->CriteriaBuilder = $this->getFactory()->buildCriteriaBuilder();
        }
        
        return $this->CriteriaBuilder;
    }

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder
     * @return void
     */
    public function setCriteriaBuilder(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder)
    {
        $this->CriteriaBuilder = $CriteriaBuilder;
    }

    /**
     * @return DataMapper\Interfaces\Criteria\Builder
     */
    public function getEntityRelationCriteria()
    {
        return $this->EntityRelationCriteria;
    }

    /**
     * @param DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder
     * @return void
     */
    public function setEntityRelationCriteria(DataMapper\Interfaces\Criteria\Builder $RelationCriteriaBuilder)
    {
        $this->EntityRelationCriteria = $RelationCriteriaBuilder;
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
    public function getData(DataMapper\Interfaces\Criteria\Builder $CriteriaBuilder=null)
    {
        if ($this->loaded) {
            return $this->Data;
        }

        $this->setupRelationParameters();
        
        if ($CriteriaBuilder !== null) {
            $this->setCriteriaBuilder($CriteriaBuilder);
        }
        if ($this->getCriteriaBuilder()->getLimit() === null) {
            $this->getCriteriaBuilder()->setLimit(10);
        }
        if ($this->getCriteriaBuilder()->getOffset() === null) {
            $this->getCriteriaBuilder()->setOffset(0);
        }

        $Loader = function () {
            if ($this->sql !== null) {
                $SqlPart = $this->getCriteriaBuilder()->toSqlPart();
                $sql = trim($this->sql.' '.$SqlPart->getSql());
                $data = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $SqlPart->getParameters())->fetchAll();
            } 
            else {
                $data = $this->getDataMapper()->fetchAll($this->getCriteriaBuilder());
            }

            if ($data === false || empty($data)) {
                return [];
            }

            $entities = [];
            foreach ($data as $item) {
                $entities[] = $this->getRepository()->buildFromArray($item, $this->getEntityRelationCriteria());
            }

            $this->loaded = true;
            return $entities;
        };

        $this->Data = new Helper\LazyCollection($Loader);
        $this->loaded = true;

        return $this->Data;
    }

    public function getCount()
    {
        if ($this->loaded) {
            return $this->count;
        }
        
        $this->setupRelationParameters();
        $CriteriaBuilder = clone $this->getCriteriaBuilder();
        $CriteriaBuilder->setOrderBy([]);
        $CriteriaBuilder->setLimit(null);
        $CriteriaBuilder->setOffset(null);
        
        if ($this->sql_count !== null) {
            $SqlPart = $CriteriaBuilder->toSqlPart();
            $sql = trim($this->sql_count.' '.$SqlPart->getSql());
            $PdoStatement = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $SqlPart->getParameters());
            $this->count = (int) $PdoStatement->fetchColumn();
        } 
        else {
            $this->count = $this->getDomainManager()->getRepositoryByName($this->getName())->count($CriteriaBuilder);
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