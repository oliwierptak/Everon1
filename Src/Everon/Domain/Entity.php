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

use Everon\Domain\Exception;
use Everon\Domain\Interfaces;
use Everon\Helper;

abstract class Entity extends Helper\Popo implements Interfaces\Entity 
{
    use Helper\IsCallable;
    
    const STATE_NEW = 1;
    const STATE_MODIFIED = 2;
    const STATE_PERSISTED = 3;
    const STATE_DELETED = 4;

    protected $id = null;

    protected $modified_properties = null;
    
    protected $state = self::STATE_NEW;


    public function __construct($id, array $data = array())
    {
        $this->id = $id;
        $this->data = $data;
        
        if ($this->isIdSet()) {
            $this->persist();
        }
    }

    public function __set($property, $value)
    {
        if (isset($this->_data[$property]) &&  $value === $this->_data[$property]) {
            return;
        }
        
        $this->markPropertyAsModified($property);
    }
    
    protected function markPropertyAsModified($property)
    {
        $this->modified_properties[$property] = true;
        $this->modify();
    }
    
    /**
     * Due to kinky behaviour of isset() and empty() cast id to string to check if it was set to something
     *
     * @return bool
     */
    protected function isIdSet()
    {
        return mb_strlen(trim($this->id)) > 0;
    }
    
    public function getModifiedProperties() 
    {
        return $this->modified_properties;
    }

    public function isPropertyModified($name) 
    {
        return (isset($this->modified_properties[$name]) && $this->modified_properties[$name] === true);
    }

    public function isNew()
    {
        return $this->state === self::STATE_NEW;
    }

    public function isModified()
    {
        return $this->state === self::STATE_MODIFIED;
    }

    public function isPersisted()
    {
        return $this->state === self::STATE_PERSISTED;
    }
    
    public function isDeleted()
    {
        return $this->state === self::STATE_DELETED;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * If the primary key is auto incremented, setId() should never be used.
     * It's database's job to maintain its primary keys.
     *
     * We either create new object (no ID) or we load it from the database (has ID).
     *
     * @param $id
     * @throws Exception\Entity
     */
    protected function setId($id)
    {
        throw new Exception\Entity('It is the database\'s job to maintain its primary keys ');
    }    

    public function getValueByName($name)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception\Entity('Invalid property name: %s', $name);
        }

        return $this->$name;
    }

    public function incept()
    {
        $this->id = null;
        $this->_data = null;
        $this->modified_properties = null;
        $this->state = self::STATE_NEW;
    }

    public function modify()
    {
        $this->state = self::STATE_MODIFIED;
    }

    public function persist()
    {
        $this->state = self::STATE_PERSISTED;
    }
    
    public function delete()
    {
        $this->state = self::STATE_DELETED;
    }

    public function __call($name, $arguments)
    {
        if ($this->isCallable($this, $name)) {
            return call_user_func([$this, $name], $arguments);
        }
        
        $return = parent::__call($name, $arguments);
        
        switch ($this->call_type) {
            case self::CALL_TYPE_SETTER:
                $this->markPropertyAsModified($this->call_property);
                break;
        }
        
        return $return;
    }
}
