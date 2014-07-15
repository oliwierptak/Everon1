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

abstract class Model implements Interfaces\Model
{
    use Domain\Dependency\Injection\DomainManager;
    use Helper\Asserts\IsArrayKey;
    use Helper\Exceptions;

    /**
     * @var string
     */
    protected $name = null;
    
    public abstract function validateEntityData(array $data);
    
    
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
        return $this->getDomainManager()->getRepository($this->getName());
    }

    protected function beforeAdd(Interfaces\Entity $Entity, $user_id)
    {
    }

    protected function beforeSave(Interfaces\Entity $Entity, $user_id)
    {
    }

    protected function beforeDelete(Interfaces\Entity $Entity, $user_id)
    {
    }

    /**
     * @param array $data
     * @param null $user_id
     * @return Interfaces\Entity
     */
    protected function add(array $data, $user_id=null)
    {
        $this->validateEntityData($data);
        $Entity = $this->getRepository()->buildFromArray($data);
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
}   