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

use Everon\Domain\Interfaces;
use Everon\Interfaces\DataMapper;
use Everon\Dependency;

abstract class Repository implements Interfaces\Repository
{
    use Dependency\Injection\DomainManager;
    
    /**
     * @var DataMapper
     */
    protected $Mapper = null;
    
    protected $name = null;
    

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
     * @param $id
     * @return Interfaces\Entity
     * @throws Exception\Repository
     */
    public function getEntityById($id)
    {
        $Criteria = (new \Everon\DataMapper\Criteria())->where([
            $this->getMapper()->getSchemaTable()->getPk() => $id
        ]);
        
        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            return null;
        }

        $data = current($data);
        $id = $this->getMapper()->getAndValidateId($data);

        return $this->getDomainManager()->getEntity($this, $id, $data);
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
