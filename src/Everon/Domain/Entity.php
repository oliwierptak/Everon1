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
use Everon\Interfaces\Arrayable;
use Everon\Interfaces\Collection;

class Entity extends Helper\Popo implements Interfaces\Entity 
{
    use Helper\Arrays;
    use Helper\IsCallable;
    use Helper\String\LastTokenToName;
    
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

    /**
     * @var string
     */
    protected $domain_name = null;

    /**
     * @var array
     */
    protected $relation_definition = [];
    

    public function __construct($id_name, array $data=[])
    {
        $this->id_name = $id_name ?: 'id';
        $this->data = $data;
        $this->RelationCollection = new Helper\Collection([]);
        
        if ($this->isIdSet()) {
            $this->markPersisted();
        }
    }
    
    protected function markPropertyAsModified($property)
    {
        if ($this->isIdSet()) { //NEW overrules MODIFIED
            $this->modified_properties[$property] = true;
            $this->markModified();
        }
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
        if (isset($this->data[$this->id_name])) {
            if (filter_var($this->data[$this->id_name], FILTER_VALIDATE_INT)) {
                return (int) $this->data[$this->id_name] > 0;
            }

            return mb_strlen(trim($this->data[$this->id_name])) > 0;
        }
        
        return false;
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
     * If the primary key is auto incremented, setId() should never be used.
     * It's the job of the database to maintain its primary keys.
     *
     * We either create new object (no ID) or we load it from the database (has ID).
     *
     * @param $id
     * @throws Exception\Entity
     */
    public function setId($id)
    {
        throw new Exception\Entity('It\'s the job of the database to maintain its primary keys.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return array_key_exists($this->id_name, $this->data) ? $this->data[$this->id_name] : null;
    }

    /**
     * @inheritdoc
     */
    public function getValueByName($name)
    {
        $name = trim($name);
        if ($name === '') {
            throw new \Everon\Domain\Exception('Property name cannot be empty in: "%s"', $this->getDomainName());
        }
        
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
        $name = trim($name);
        if ($name === '') {
            throw new \Everon\Domain\Exception('Property name cannot be empty in: "%s"', $this->getDomainName());
        }
        $this->data[$name] = $value;
        $this->markPropertyAsModified($name);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->markDeleted();
        $this->modified_properties = null;
        $this->data = [];
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
        $data = parent::toArray($deep);

        if ($deep) {
            foreach ($this->getRelationCollection() as $Relation) {
                /**
                 * @var \Everon\Domain\Interfaces\Relation $Relation
                 */
                if ($Relation->getType() !== Relation::ONE_TO_ONE && $Relation->getType() !== Relation::ONE_TO_MANY) {
                    continue;
                }
                
                $One = $Relation->getOne();
                if ($One instanceof Arrayable) {
                    $data[$Relation->getName()] = $One->toArray(false); //false because of circular references
                }
            }
        }
        
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function setRelationCollection(Collection $RelationCollection)
    {
        $this->RelationCollection = $RelationCollection;
    }

    /**
     * @inheritdoc
     */
    public function getRelationCollection()
    {
        return $this->RelationCollection;
    }

    /**
     * @inheritdoc
     */
    public function setRelationByName($name, Interfaces\Relation $Relation)
    {
        $this->RelationCollection->set($name, $Relation);
    }

    /**
     * @inheritdoc
     */
    public function getRelationByName($name)
    {
        if ($this->RelationCollection->has($name) === false) {
            throw new Exception('Missing relation: "%s" for "%s"', [$name, $this->getDomainName()]);
        }
        return $this->RelationCollection->get($name);
    }

    /**
     * @inheritdoc
     */
    public function hasRelation($name)
    {
        return $this->RelationCollection->has($name);
    }

    /**
     * @inheritdoc
     */
    public function getDomainName()
    {
        if ($this->domain_name === null) {
            $tokens = explode('\\', get_class($this));
            $this->domain_name = trim(@$tokens[2]);
            if ($this->domain_name === '') {
                throw new \Everon\Exception\Domain('Could not determine the domain name for entity: "%s"', get_class($this));
            }
        }
        
        return $this->domain_name;
    }

    /**
     * @return array
     */
    public function getRelationDefinition()
    {
        return $this->relation_definition;
    }

    /**
     * @param array $relation_definition
     */
    public function setRelationDefinition(array $relation_definition)
    {
        $this->relation_definition = $relation_definition;
    }

    /**
     * @param $name
     */
    public function resetRelationState($name)
    {
        $this->getRelationByName($name)->reset();
    }

    /**
     * @param array $data
     */
    public function updateValues(array $data)
    {
        $data = $this->arrayMergeDefault($this->getData(), $data);
        $this->setData($data);
        $this->modified_properties = array_keys($data);
        $this->markModified();
    }
    
    /**
     * Does the usual call but also marks properties as modified when setter is used
     * 
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception\Entity
     */
    public function __call($name, $arguments)
    {
        $return = parent::__call($name, $arguments);
        
        if ($this->call_type === static::CALL_TYPE_SETTER) {
            $this->markPropertyAsModified($this->call_property);
        }
        
        return $return;
    }
}
