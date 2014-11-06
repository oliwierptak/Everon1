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

use Everon\Dependency;
use Everon\Domain;
use Everon\Helper;
use Everon\Rest;

abstract class Model implements Interfaces\Model
{
    use Dependency\Injection\Factory;
    use Domain\Dependency\Injection\DomainManager;
    
    use Helper\Arrays;
    use Helper\Asserts\IsArrayKey;
    use Helper\Exceptions;

    /**
     * @var string
     */
    protected $name = null;
    
    
    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getRepository()
    {
        return $this->getDomainManager()->getRepositoryByName($this->getName());
    }

    /**
     * @inheritdoc
     */
    public function setRepository(Domain\Interfaces\Repository $Repository)
    {
        $this->getDomainManager()->setRepositoryByName($this->getName(), $Repository);
    }

    /**
     * @param array $data
     * @return Interfaces\Entity
     */
    public function create(array $data=[])
    {
        $data[$this->getRepository()->getMapper()->getTable()->getPk()] = null;
        return $this->getRepository()->buildFromArray($data);
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     */
    protected function beforeAdd(Interfaces\Entity $Entity, $user_id=null)
    {
        
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     */
    protected function beforeSave(Interfaces\Entity $Entity, $user_id=null)
    {
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     */
    protected function beforeDelete(Interfaces\Entity $Entity, $user_id=null)
    {
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     * @return Interfaces\Entity
     */
    protected function add(Domain\Interfaces\Entity $Entity, $user_id=null)
    {
        $this->beforeAdd($Entity, $user_id);
        $this->getRepository()->persist($Entity, $user_id);
        return $Entity;
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     * @return Interfaces\Entity
     */
    protected function save(Domain\Interfaces\Entity $Entity, $user_id=null)
    {
        $this->beforeSave($Entity, $user_id);
        $this->getRepository()->persist($Entity, $user_id);
        return $Entity;
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     */
    protected function delete(Domain\Interfaces\Entity $Entity, $user_id=null)
    {
        $this->beforeDelete($Entity, $user_id);
        $this->getRepository()->remove($Entity, $user_id);
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        return $this->getRepository()->getEntityById($id);
    }

    /**
     * @inheritdoc
     */
    public function addCollection(Domain\Interfaces\Entity $Entity, $relation_name, array $data, $user_id=null)
    {
        $Repository = $this->getDomainManager()->getRepositoryByName($relation_name);
        try {
            $Repository->beginTransaction();
            /**
             * @var Domain\Interfaces\Entity $EntityToAdd
             */
            $method = "add{$relation_name}";
            
            foreach ($data as $item_data) {
                $EntityToAdd = $this->getDomainManager()->getModelByName($relation_name)->create($item_data);
                if ($EntityToAdd->isNew() === false) {
                    throw new Domain\Exception('Only new entities can be added. Entity: "%s" is not marked as: "NEW"', $EntityToAdd->getDomainName());
                }
                
                $Relation = $EntityToAdd->getRelationByName($Entity->getDomainName());
                if ($Relation->getRelationMapper()->isOwningSide() === false) {
                    //make sure the referenced column in child entity is set to parent entity's assigned values, eg. TicketConversation.ticket_id = Ticket.id
                    $EntityToAdd->setValueByName($Relation->getRelationMapper()->getMappedBy(), $Entity->getValueByName($Relation->getRelationMapper()->getInversedBy()));
                }
                $this->getDomainManager()->getModelByName($relation_name)->{$method}($EntityToAdd, $user_id);
            }
            $Repository->commitTransaction();
        }
        catch (\Exception $e) {
            $Repository->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function saveCollection(Domain\Interfaces\Entity $Entity, $relation_name, array $data, $user_id=null)
    {
        $Repository = $this->getDomainManager()->getRepositoryByName($relation_name);
        try {
            $Repository->beginTransaction();
            /**
             * @var Domain\Interfaces\Entity $EntityToSave
             */
            $method = "save{$relation_name}";
    
            foreach ($data as $item_data) {
                $EntityToSave = $this->getDomainManager()->getModelByName($relation_name)->create($item_data);
                if ($EntityToSave->isPersisted() === false) {
                    throw new Domain\Exception('Only existing entities can be saved. Entity: "%s" is not marked as: "PERSISTED"', $EntityToSave->getDomainName());
                }
                
                $EntityToCheck = $Repository->getEntityById($EntityToSave->getId());
                if ($EntityToCheck === null) {
                    throw new Domain\Exception('Entity: "%s" with id: "%s" does not exist', [$EntityToSave->getDomainName(), $EntityToSave->getId()]);
                }
                
                $Relation = $EntityToSave->getRelationByName($Entity->getDomainName());
                if ($Relation->getRelationMapper()->isOwningSide() === false) {
                    //make sure the referenced column in child entity is set to parent entity's assigned values, eg. TicketConversation.ticket_id = Ticket.id
                    $EntityToSave->setValueByName($Relation->getRelationMapper()->getMappedBy(), $Entity->getValueByName($Relation->getRelationMapper()->getInversedBy()));
                }
                $this->getDomainManager()->getModelByName($relation_name)->{$method}($EntityToSave, $user_id);
            }
            $Repository->commitTransaction();
        }
        catch (\Exception $e) {
            $Repository->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteCollection(Domain\Interfaces\Entity $Entity, $relation_name, array $data, $user_id=null)
    {
        $Repository = $this->getDomainManager()->getRepositoryByName($relation_name);
        try {
            $Repository->beginTransaction();
            /**
             * @var Domain\Interfaces\Entity $EntityToDelete
             */
            $method = "delete{$relation_name}";
    
            foreach ($data as $item_data) {
                //check if id field exists in item data
                $pk_name = $Repository->getMapper()->getTable()->getPk();
                if (array_key_exists($pk_name, $item_data) === false) {
                    throw new Domain\Exception('Entity: "%s" is missing its ID value', $EntityToDelete->getDomainName());
                }
    
                //check if item data consist only of id field(s)
                $pk_incoming_count = count($item_data);
                $pk_count = count($Repository->getMapper()->getTable()->getPrimaryKeys());            
                if ($pk_incoming_count !== $pk_count) {
                    throw new Domain\Exception('Entity: "%s" primary keys mismatch. Expected "%s", received: "%s" primary keys', [$relation_name, $pk_count, $pk_incoming_count]);
                }
    
                $EntityToDelete = $this->getDomainManager()->getModelByName($relation_name)->create($item_data);
                if ($EntityToDelete->isPersisted() === false) {
                    throw new Domain\Exception('Only existing entities can be deleted. Entity: "%s" is not marked as: "PERSISTED"', $EntityToDelete->getDomainName());
                }
    
                $EntityToCheck = $Repository->getEntityById($EntityToDelete->getId());
                if ($EntityToCheck === null) {
                    throw new Domain\Exception('Entity: "%s" with id: "%s" does not exist', [$EntityToDelete->getDomainName(), $EntityToDelete->getId()]);
                }
    
                $Relation = $EntityToDelete->getRelationByName($Entity->getDomainName());
                if ($Relation->getRelationMapper()->isOwningSide() === false) {
                    //make sure the referenced column in child entity is set to parent entity's assigned values, eg. TicketConversation.ticket_id = Ticket.id
                    $EntityToDelete->setValueByName($Relation->getRelationMapper()->getMappedBy(), $Entity->getValueByName($Relation->getRelationMapper()->getInversedBy()));
                }
                $this->getDomainManager()->getModelByName($relation_name)->{$method}($EntityToDelete, $user_id);
            }
            $Repository->commitTransaction();
        }
        catch (\Exception $e) {
            $Repository->rollbackTransaction();
            throw $e;
        }
    }
}   