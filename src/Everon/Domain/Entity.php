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
use Everon\Interfaces\Collection;

class Entity extends Helper\Popo implements Interfaces\Entity 
{
    const STATE_NEW = 1;
    const STATE_MODIFIED = 2;
    const STATE_PERSISTED = 3;
    const STATE_DELETED = 4;

    protected $id = null;

    protected $modified_properties = null;
    
    protected $state = self::STATE_NEW;

    protected $methods = null;

    /**
     * @var Collection
     */
    protected $RelationCollection = null;


    public function __construct($id, array $data=[])
    {
        $this->id = $id;
        $this->data = $data;
        $this->methods = array_flip(get_class_methods(get_class($this))); //faster lookup then using isCallable()
        $this->RelationCollection = new Helper\Collection([]);
        
        if ($this->isIdSet()) {
            $this->markPersisted();
        }
    }

    protected function markPropertyAsModified($property)
    {
        $this->modified_properties[$property] = true;
        $this->markModified();
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
        throw new Exception\Entity('It is the database\'s job to maintain primary keys');
    }

    protected function markModified()
    {
        $this->state = static::STATE_MODIFIED;
    }

    protected function markPersisted()
    {
        $this->state = static::STATE_PERSISTED;
    }

    protected function markDeleted()
    {
        $this->state = static::STATE_DELETED;
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

    /**
     * @return array
     */
    public function getModifiedProperties() 
    {
        return $this->modified_properties;
    }

    /**
     * @param $name
     * @return bool
     */
    public function isPropertyModified($name) 
    {
        return (isset($this->modified_properties[$name]) && $this->modified_properties[$name] === true);
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->state === static::STATE_NEW;
    }

    /**
     * @return bool
     */
    public function isModified()
    {
        return $this->state === static::STATE_MODIFIED;
    }

    /**
     * @return bool
     */
    public function isPersisted()
    {
        return $this->state === static::STATE_PERSISTED;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->state === static::STATE_DELETED;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception\Entity
     */
    public function getValueByName($name)
    {
        try {
            return $this->data[$name];
        }
        catch (\Exception $e) {
            throw new Exception\Entity('Invalid property name: %s', $name);
        }
    }

    /**
     * @param $name
     * @param mixed $value
     */
    public function setValueByName($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function delete()
    {
        $this->markDeleted();
        $this->id = null;
        $this->modified_properties = null;
        $this->data = null;
    }
    
    public function persist($id, array $data)
    {
        $this->markPersisted();
        $this->id = $id;
        $this->data = $data;
        $this->modified_properties = null;
    }

    /**
     * @param Collection $RelationCollection
     */
    public function setRelationCollection(Collection $RelationCollection)
    {
        $this->RelationCollection = $RelationCollection;
    }

    /**
     * @return Collection
     */
    public function getRelationCollection()
    {
        return $this->RelationCollection;
    }
    
    /**
     * Does the usual call but also marks properties as modified when setter is used
     * 
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->methods[$name])) {
            $this->call_type = static::CALL_TYPE_METHOD;
            $this->call_property = $name;
            return call_user_func_array([$this, $name], $arguments);
        }

        $return = parent::__call($name, $arguments);
        
        switch ($this->call_type) {
            case static::CALL_TYPE_SETTER:
                $this->markPropertyAsModified($this->call_property);
                break;
        }
        
        return $return;
    }

    public function __sleep()
    {
        //todo: test me xxx
        return [
            'id', 
            'data',
            'modified_properties',
            'state',
            'methods',
            'call_type',
            'call_property',
        ];
    }

    public static function __set_state(array $array)
    {
        //todo: test me xxx
        $Entity = new static($array['id'], $array['data']);
        $Entity->modified_properties = $array['modified_properties'];
        $Entity->state = $array['state'];
        $Entity->methods = $array['methods'];
        $Entity->call_type = $array['call_type'];
        $Entity->call_property = $array['call_property'];
        return $Entity;
    }
}
