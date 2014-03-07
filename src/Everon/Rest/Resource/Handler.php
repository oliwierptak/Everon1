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

use Everon\DataMapper\Criteria;
use Everon\Dependency;
use Everon\Domain\Interfaces\Entity;
use Everon\Rest\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Rest\Interfaces;

class Handler implements Interfaces\ResourceHandler
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\DomainManager;
    use Dependency\Injection\Request; //todo meh
    use Helper\AlphaId;
    use Helper\Arrays;
    use Helper\Asserts\IsInArray;
    use Helper\Asserts\IsNull;
    use Helper\Exceptions;

    const VERSIONING_URL = 'url';
    const VERSIONING_HEADER = 'header';
    
    const ALPHA_ID_SALT = 'Vhg656';


    /**
     * @var array
     */
    protected $supported_versions = ['v1', 'v2']; //todo read from config

    /**
     * @var string Versioning type. Accepted values are: 'url' or 'header'
     */
    protected $versioning = 'url';
    
    protected $current_version = null;  //v1, v2, v3... todo: remove this property, handler can be version agnostic
    
    protected $url = null;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $MappingCollection = null;
    

    /**
     * @param $url
     * @param $version
     * @param $versioning
     */
    public function __construct($url, $version, $versioning, array $mappings)
    {
        $this->url = $url;
        $this->current_version = $version;
        $this->versioning = $versioning;
        $this->MappingCollection = new Helper\Collection($mappings);
    }
    
    protected function buildResourceFromEntity(Entity $Entity, $resource_name, $version)
    {
        $version = $version ?: $this->current_version;
        $this->assertIsInArray($version, $this->supported_versions, 'Unsupported version: "%s"', 'Domain');
        
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $resource_id = $this->generateResourceId($Entity->getId(), $resource_name);
        $href = $this->getResourceUrl($resource_id, $resource_name);

        $Resource = $this->getFactory()->buildRestResource($domain_name, $version, $href, $Entity); //todo: change version to href
        $this->buildResourceRelations($Resource);

        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function getResource($resource_id, $resource_name, $version, $collection)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $Entity = $Repository->getEntityById($id);

            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" not found', $id), 'Domain');
            
            $Resource =  $this->buildResourceFromEntity($Entity, $resource_name, $version);

            if ($collection !== null) {
                $domain_name = $Resource->getRelationDomainName($collection);
                if ($domain_name !== null) {
                    $Relation = $Resource->getDomainEntity()->getRelationCollection()[$domain_name];
                    $r = $Relation->toArray();

                    $Relation = new Helper\Collection([]);
                    for ($a=0 ;$a<count($r); $a++) {
                        $CollectionEntity = $r[$a];
                        $Relation->set($a, $this->buildResourceFromEntity($CollectionEntity, $collection, $version));
                    }
                    
                    $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $version, $Resource->getHref().'/'.$collection, $Relation); //todo: change version to href
                    $CollectionResource->setLimit($this->getRequest()->getGetParameter('limit', 10));
                    $CollectionResource->setOffset($this->getRequest()->getGetParameter('offset', 0));
                    
                    $Resource->setRelationResourceByName($collection, $CollectionResource);
                }
            }
            
            return $Resource;
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('Resource: "%s" not found', [$this->getResourceUrl($resource_id, $resource_name)], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCollection22($resource_name, $version)
    {
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $Resource = $this->getResource($resource_id, $resource_name, $version);
        $href = $Resource->getHref().'/'.$collection;
        $Entity = $Resource->getDomainEntity();
        foreach ($Resource->getRelationDefinition() as $resource_name => $resource_domain_name) {
            if ($resource_name === $collection) {
                $Collection = $Entity->getRelationCollection()[$resource_domain_name];

                $collection_list = $Collection->toArray();
                $ResourceList = new Helper\Collection([]);
                for ($a=0; $a<count($collection_list); $a++) {
                    $CollectionEntity = $collection_list[$a];
                    $entity_resource_id = $this->generateResourceId($CollectionEntity->getId(), $resource_name);
                    $ResourceList->set($a, $this->getResource($entity_resource_id, $resource_name, $version));
                }

                $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $version, $href, $Collection); //todo: change version to href
                $CollectionResource->setLimit($this->getRequest()->getGetParameter('limit', 10));
                $CollectionResource->setOffset($this->getRequest()->getGetParameter('offset', 0));
                $Resource->setRelationResourceByName($resource_name, $CollectionResource);
            }
        }

        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function getCollectionResource($resource_name, $version)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $href = $this->getResourceUrl(null, $resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $Criteria = new Criteria();
            $Criteria->limit($this->getRequest()->getGetParameter('limit', 10));
            $Criteria->offset($this->getRequest()->getGetParameter('offset', 0));
            
            $entity_list = $Repository->getList($Criteria);
    
            $ResourceList = new Helper\Collection([]);
            for ($a=0; $a<count($entity_list); $a++) {
                $CollectionEntity = $entity_list[$a];
                $ResourceList->set($a, $this->buildResourceFromEntity($CollectionEntity, $resource_name, $version));
            }
            
            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $version, $href, $ResourceList); //todo: change version to href
            $CollectionResource->setLimit($this->getRequest()->getGetParameter('limit', 10));
            $CollectionResource->setOffset($this->getRequest()->getGetParameter('offset', 0));
            return $CollectionResource;
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('CollectionResource: "%s" not found', [$this->getResourceUrl(null, $resource_name)], $e);
        }
    }

    public function buildResourceRelations(Interfaces\Resource $Resource)
    {
        /**
         * @var \Everon\Domain\Interfaces\Zone\Entity $Entity
         */
        //$Entity = $Resource->getDomainEntity();
        $RelationCollection = new Helper\Collection([]);
        foreach ($Resource->getRelationDefinition() as $resource_name => $resource_domain_name) {
            //$Collection = $this->getCollection($resource_name, $this->current_version);
            //$RelationCollection->set($resource_name, $Collection);
            $RelationCollection->set($resource_name, ['href' => $Resource->getHref().'/'.$resource_name]);
        }

        $Resource->setRelationCollection($RelationCollection);
    }

    /**
     * @inheritdoc
     */
    public function generateEntityId($resource_id, $name)
    {
        return $resource_id;
        $name .= static::ALPHA_ID_SALT;
        return $this->alphaId($resource_id, true, 7, $name);
    }

    /**
     * @inheritdoc
     */
    public function generateResourceId($entity_id, $name)
    {
        return $entity_id;
        $name .= static::ALPHA_ID_SALT;
        return $this->alphaId($entity_id, false, 7, $name);
    }

    /**
     * @inheritdoc
     */
    public function getResourceUrl($resource_id, $name)
    {
        $resource_id = trim($resource_id) !== '' ? '/'.$resource_id : '';
        return $this->getUrl().$name.$resource_id;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        switch ($this->versioning) {
            case static::VERSIONING_URL:
                return $this->url.$this->current_version.'/';        
                break;
            
            default:
                return $this->url;
                break;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getDomainNameFromMapping($resource_name)
    {
        $domain_name = $this->MappingCollection->get($resource_name, null);
        if ($domain_name === null) {
            throw new Exception\Manager('Invalid rest mapping domain: "%s"', $resource_name);
        }
        
        return $domain_name;
    }

    /**
     * @inheritdoc
     */
    public function getResourceNameFromMapping($domain_name)
    {
        $resource_name = $this->MappingCollection->get($domain_name, null);
        if ($resource_name === null) {
            throw new Exception\Manager('Invalid rest mapping resource: "%s"', $domain_name);
        }

        return $resource_name;
    }
}