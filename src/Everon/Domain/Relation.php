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
     * @var DataMapper\Interfaces\Criteria
     */
    protected $Criteria = null;

    /**
     * @var DataMapper\Interfaces\Criteria
     */
    protected $RelationCriteria = null;

    /**
     * @var Domain\Interfaces\Repository
     */
    protected $Entity = null;

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
     * @param Interfaces\Entity $Entity
     */
    public function __construct(Domain\Interfaces\Entity $Entity)
    {
        $this->Entity = $Entity;
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->Data = new Helper\Collection([]);
    }

    abstract protected function setupRelationParameters();

    public function reset()
    {
        $this->loaded = false;
    }

    /**
     * @return array
     */
    protected function getToArray()
    {
        return $this->getData()->toArray();
    }

    /**
     * @return Domain\Interfaces\Entity
     */
    public function getEntity()
    {
        return $this->Entity;
    }

    /**
     * @param Domain\Interfaces\Entity $Entity
     */
    public function setEntity(Domain\Interfaces\Entity $Entity)
    {
        $this->Entity = $Entity;
        $this->reset();
    }

    /**
     * @return DataMapper\Interfaces\Criteria
     */
    public function getCriteria()
    {
        if ($this->Criteria === null) {
            $this->Criteria = (new \Everon\DataMapper\Criteria())->limit(10)->offset(0);
        }

        return $this->Criteria;
    }

    /**
     * @param DataMapper\Interfaces\Criteria $Criteria
     */
    public function setCriteria(DataMapper\Interfaces\Criteria $Criteria)
    {
        $this->Criteria = $Criteria;
        $this->reset();
    }

    /**
     * @return DataMapper\Interfaces\Criteria
     */
    public function getRelationCriteria()
    {
        return $this->RelationCriteria;
    }

    /**
     * @param DataMapper\Interfaces\Criteria $RelationCriteria
     */
    public function setRelationCriteria(DataMapper\Interfaces\Criteria $RelationCriteria)
    {
        $this->RelationCriteria = $RelationCriteria;
        $this->reset();
    }

    /**
     * @return \Everon\Interfaces\DataMapper
     */
    public function getDataMapper()
    {
        return $this->getDomainManager()->getRepository($this->getName())->getMapper();
    }

    /**
     * @param \Everon\Interfaces\DataMapper $DataMapper
     */
    public function setDataMapper(\Everon\Interfaces\DataMapper $DataMapper)
    {
        $this->getDomainManager()->getRepository($this->getName())->setMapper($DataMapper);
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
        return $this->getDomainManager()->getRepository($this->getName());
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
    public function getData()
    {
        $this->setupRelationParameters();

        if ($this->loaded === false) {
            $Loader = function () {
                if ($this->sql !== null) {
                    $sql = $this->sql.$this->getCriteria();
                    $data = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $this->getCriteria()->getWhere())->fetchAll();
                } else {
                    $data = $this->getDataMapper()->fetchAll($this->getCriteria());
                }


                if ($data === false || empty($data)) {
                    return [];
                }

                $entities = [];
                foreach ($data as $item) {
                    $entities[] = $this->getRepository()->buildFromArray($item, $this->getRelationCriteria());
                }

                return $entities;
            };

            $this->Data = new Helper\LazyCollection($Loader);
            $this->loaded = true;
        }

        return $this->Data;
    }

    public function getCount()
    {
        $this->setupRelationParameters();

        $Criteria = clone $this->getCriteria();
        $Criteria->orderBy(null);

        if ($this->loaded === false) {
            if ($this->sql_count !== null) {
                $sql = $this->sql_count.' '.$Criteria;
                $PdoStatement = $this->getDataMapper()->getSchema()->getPdoAdapterByName('read')->execute($sql, $Criteria->getWhere());
                $this->count = (int) $PdoStatement->fetchColumn();
            } else {
                return $this->getDomainManager()->getRepository($this->getName())->count($Criteria);
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