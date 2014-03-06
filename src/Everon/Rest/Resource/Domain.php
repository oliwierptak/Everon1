<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Helper;
use Everon\Domain\Interfaces\Entity;

abstract class Domain extends \Everon\Rest\Resource implements \Everon\Rest\Interfaces\ResourceDomain
{
    /**
     * @var Entity
     */
    protected $DomainEntity = null;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $RelationCollection = null;

    protected $relation_definition = [];


    public function __construct($name, $version, Entity $Entity)
    {
        parent::__construct($name, $version, $Entity->toArray());
        $this->DomainEntity = $Entity;
        $this->RelationCollection = new Helper\Collection([]);
    }
    
    protected function getToArray()
    {
        $data = parent::getToArray();
        //    $this->data = $this->DomainEntity->toArray();
        if (empty($this->RelationCollection) === false) {
            $data = array_merge($this->data, $this->RelationCollection->toArray());
        }
        
        return $data;
    }
    
    /**
     * @inheritdoc
     */
    public function getDomainEntity()
    {
        return $this->DomainEntity;
    }

    /**
     * @return array
     */
    public function getRelationDefinition()
    {
        return $this->relation_definition;
    }

    /**
     * @param \Everon\Interfaces\Collection $RelationCollection
     */
    public function setRelationCollection(\Everon\Interfaces\Collection $RelationCollection)
    {
        $this->RelationCollection = $RelationCollection;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getRelationCollection()
    {
        return $this->RelationCollection;
    }
}
