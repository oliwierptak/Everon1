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
    use Helper\IsCallable;
    
    const STATE_NEW = 1;
    const STATE_MODIFIED = 2;
    const STATE_PERSISTED = 3;
    const STATE_DELETED = 4;

    protected $id_name = null;

    protected $modified_properties = null;
    
    protected $state = self::STATE_NEW;

    /**
     * @var Collection
     */
    protected $RelationCollection = null;
    

    public function __construct($id_name, array $data=[])
    {
        $this->id_name = $id_name;
        $this->data = $data;
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
        $id = trim($this->data[$this->id_name]);
        return mb_strlen($id) > 0;
    }

    /**
     * @inheritdoc
     */
    public function getModifiedProperties() 
    {
        return $this->modified_properties;
    }

    /**
     * @inheritdoc
     */
    public function isPropertyModified($name) 
    {
        return (isset($this->modified_properties[$name]) && $this->modified_properties[$name] === true);
    }

    /**
     * @inheritdoc
     */
    public function isNew()
    {
        return $this->state === static::STATE_NEW;
    }

    /**
     * @inheritdoc
     */
    public function isModified()
    {
        return $this->state === static::STATE_MODIFIED;
    }

    /**
     * @inheritdoc
     */
    public function isPersisted()
    {
        return $this->state === static::STATE_PERSISTED;
    }

    /**
     * @inheritdoc
     */
    public function isDeleted()
    {
        return $this->state === static::STATE_DELETED;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->data[$this->id_name];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setValueByName($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    
    public function delete()
    {
        $this->markDeleted();
        $this->modified_properties = null;
        $this->data = null;
    }

    /**
     * @inheritdoc
     */
    public function persist(array $data)
    {
        $this->markPersisted();
        $this->data = $data;
        $this->modified_properties = null;
    }

    /**
     * @inheritdoc
     */
    public function toArray($deep=false)
    {
        return parent::toArray($deep);
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
     * @param $name
     * @param Collection $CollectionResource
     */
    public function setRelationCollectionByName($name, Collection $CollectionResource)
    {
        $this->RelationCollection->set($name, $CollectionResource);
    }

    /**
     * @param $name
     * @return Collection
     */
    public function getRelationCollectionByName($name)
    {
        return $this->RelationCollection->get($name);
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
        if ($this->isCallable($this, $name)) {
            $this->call_type = static::CALL_TYPE_METHOD;
            $this->call_property = $name;
            return call_user_func_array([$this, $name], $arguments);
        }

        $return = parent::__call($name, $arguments);
        
        if ($this->call_type === static::CALL_TYPE_SETTER) {
            $this->markPropertyAsModified($this->call_property);
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
        $Entity->call_type = $array['call_type'];
        $Entity->call_property = $array['call_property'];
        return $Entity;
    }
}
