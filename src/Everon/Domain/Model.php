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

use Everon\Domain;
use Everon\Helper;
use Everon\Rest;

abstract class Model implements Interfaces\Model
{
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
     * @param array $data
     * @return Interfaces\Entity
     */
    public function create(array $data=[])
    {
        return $this->getRepository()->buildFromArray($data);
    }

    protected function beforeAdd(Interfaces\Entity $Entity, $user_id=null)
    {
        
    }

    protected function beforeSave(Interfaces\Entity $Entity, $user_id=null)
    {
    }

    protected function beforeDelete(Interfaces\Entity $Entity, $user_id=null)
    {
    }

    /**
     * @param Interfaces\Entity $Entity
     * @param null $user_id
     * @internal param array $data
     * @return Interfaces\Entity
     */
    protected function add(Domain\Interfaces\Entity $Entity, $user_id=null)
    {
        $this->getRepository()->validateEntity($Entity);
        $this->beforeAdd($Entity, $user_id);
        $this->getRepository()->persist($Entity, $user_id);
        return $Entity;
    }

    /**
     * @param array $data
     * @param null $user_id
     * @return Interfaces\Entity
     */
    protected function save(array $data, $user_id=null)
    {
        $this->validateEntityData($data);
        $Entity = $this->getRepository()->buildFromArray($data);
        $this->beforeSave($Entity, $user_id);
        $this->getRepository()->persist($Entity, $user_id);
        return $Entity;
    }

    /**
     * @param $id
     * @param null $user_id
     */
    protected function delete($id, $user_id=null)
    {
        $Entity = $this->getRepository()->getEntityById($id);
        $this->beforeDelete($Entity, $user_id);
        $this->getRepository()->remove($Entity, $user_id);
    }

    /**
     * @param Domain\Interfaces\Entity $Entity
     * @param $relation_name
     * @param array $data
     * @param null $user_id
     */
    public function addCollection(Domain\Interfaces\Entity $Entity, $relation_name, array $data, $user_id = null)
    {
        /**
         * @var Domain\Interfaces\Entity $EntityToAdd
         */
        $relation_name_formatted = strtolower($relation_name);
        $method = "add{$relation_name_formatted}";
        
        foreach ($data as $item_data) {
            $EntityToAdd = $this->getDomainManager()->getRepositoryByName($relation_name)->buildFromArray($item_data);
            
            $this->getDomainManager()->getModelByName($relation_name)->{$method}($EntityToAdd->toArray(), $user_id);
        }
    }
}   