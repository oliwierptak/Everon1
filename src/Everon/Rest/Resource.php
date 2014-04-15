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
    use Helper\String\LastTokenToName;
    
    /**
     * @var Entity
     */
    protected $DomainEntity = null;

    /**
     * @var Collection
     */
    protected $RelationCollection = null;
    
    /**
     * @param $href
     * @param $version
     * @param $resource_name
     * @param $domain_name
     * @param Entity $Entity
     */
    public function __construct(Interfaces\ResourceHref $Href, Entity $Entity)
    {
        parent::__construct($Href);
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
    
    public function getDomainName()
    {
        return $this->getDomainEntity()->getDomainName();
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
    public function setRelationCollectionByName($name, Interfaces\ResourceCollection $CollectionResource)
    {
        $this->RelationCollection->set($name, $CollectionResource);
    }

    /**
     * @inheritdoc
     */
    public function getRelationCollectionByName($name)
    {
        return $this->RelationCollection->get($name);
    }

    protected function getToArray()
    {
        $data = parent::getToArray();
        $data = array_merge($data, $this->DomainEntity->toArray());
        $data = array_merge($data, $this->RelationCollection->toArray(true));
        
        return $data;
    }
}
