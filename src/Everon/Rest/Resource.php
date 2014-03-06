<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Helper;
use Everon\Domain\Interfaces\Entity;
use Everon\Interfaces\Collection;


abstract class Resource extends Resource\Basic implements Interfaces\Resource
{
    /**
     * @var Entity
     */
    protected $DomainEntity = null;

    /**
     * @var Collection
     */
    protected $RelationCollection = null;

    /**
     * @var array
     */
    protected $relation_definition = [];
    

    public function __construct($name, $version, $href, Entity $Entity)
    {
        parent::__construct($name, $version, $href);
        $this->DomainEntity = $Entity;
        $this->RelationCollection = new Helper\Collection([]);
    }

    /**
     * @inheritdoc
     */
    public function getDomainEntity()
    {
        return $this->DomainEntity;
    }

    /**
     * @inheritdoc
     */
    public function getRelationDefinition()
    {
        return $this->relation_definition;
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
    
    protected function getToArray()
    {
        $data = parent::getToArray();
        $data = array_merge($data, $this->DomainEntity->toArray());
        
        if (empty($this->RelationCollection) === false) {
            $data = array_merge($data, $this->RelationCollection->toArray());
        }

        return $data;
    }
}
