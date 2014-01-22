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

class Entity extends Helper\Popo implements Interfaces\Entity 
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
        $this->state = self::STATE_MODIFIED;
    }

    protected function markPersisted()
    {
        $this->state = self::STATE_PERSISTED;
    }

    protected function markDeleted()
    {
        $this->state = self::STATE_DELETED;
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
        return $this->state === self::STATE_NEW;
    }

    /**
     * @return bool
     */
    public function isModified()
    {
        return $this->state === self::STATE_MODIFIED;
    }

    /**
     * @return bool
     */
    public function isPersisted()
    {
        return $this->state === self::STATE_PERSISTED;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->state === self::STATE_DELETED;
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
        if (array_key_exists($name, $this->data) === false) {
            throw new Exception\Entity('Invalid property name: %s', $name);
        }

        return $this->data[$name];
    }

    /**
     * @param $name
     * @param mixed $value
     */
    public function setValueByName($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function persist()
    {
        $this->markPersisted();
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
            $this->call_type = self::CALL_TYPE_METHOD;
            $this->call_property = $name;
            return call_user_func_array([$this, $name], $arguments);
        }

        $return = parent::__call($name, $arguments);
        
        switch ($this->call_type) {
            case self::CALL_TYPE_SETTER:
                $this->markPropertyAsModified($this->call_property);
                break;
        }
        
        return $return;
    }

    public function __sleep()
    {
        return [
            'id', 
            'data',
            'modified_properties',
            'state',
        ];
    }

    public static function __set_state(array $array)
    {
        $Entity = new self($array['id'], $array['data']);
        $Entity->modified_properties = $array['modified_properties'];
        $Entity->state = $array['state'];
        return $Entity;
    }
}
