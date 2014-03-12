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
    protected $supported_versions = null;

    /**
     * @var string Versioning type. Accepted values are: 'url' or 'header'
     */
    protected $versioning = null;
    
    protected $current_version = null;  //v1, v2, v3...
    
    protected $url = null;  //http://api.localhost:80/

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $MappingCollection = null;


    /**
     * @param $url
     * @param array $supported_versions
     * @param $versioning
     * @param array $mapping
     */
    public function __construct($url, array $supported_versions, $versioning, array $mapping)
    {
        $this->url = $url;
        $this->supported_versions = $supported_versions;
        $this->current_version = $this->supported_versions[count($supported_versions)-1];
        $this->versioning = $versioning;
        $this->MappingCollection = new Helper\Collection($mapping);
    }

    /**
     * @param Entity $Entity
     * @param $resource_name
     * @param $version
     * @return Interfaces\Resource
     */
    protected function buildResourceFromEntity(Entity $Entity, $resource_name, $version)
    {
        $this->assertIsInArray($version, $this->supported_versions, 'Unsupported version: "%s"', 'Domain');
        
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $resource_id = $this->generateResourceId($Entity->getId(), $resource_name);
        $link = $this->getResourceUrl($version, $resource_name, $resource_id);

        $Resource = $this->getFactory()->buildRestResource($domain_name, $version, $link, $Entity); //todo: change version to href
        $this->buildResourceRelations($Resource, $resource_id, $resource_name, $version);

        return $Resource;
    }


    public function add($version, $resource_name, array $data)
    {
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $Repository = $this->getDomainManager()->getRepository($domain_name);
        $Entity = $Repository->addFromArray($data);
        //$Entity = $this->getDomainManager()->buildEntity($Repository, null, $data);
        sd($Entity, $version, $resource_name, $data);
    }

    /**
     * @inheritdoc
     */
    public function getResource($resource_id, $resource_name, $version, Interfaces\ResourceNavigator $Navigator)
    {
        try {
            $EntityRelationCriteria = new Criteria();
            $EntityRelationCriteria->limit($Navigator->getLimit());
            $EntityRelationCriteria->offset($Navigator->getOffset());
            
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $Entity = $Repository->getEntityById($id, $EntityRelationCriteria);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" not found', $domain_name), 'Domain');
            $Resource =  $this->buildResourceFromEntity($Entity, $resource_name, $version);
            $link = $this->getResourceUrl($version, $resource_name, $resource_id);
            $Resource->setHref($link);

            $resources_to_expand = $Navigator->getExpand();
            foreach ($resources_to_expand as $collection_name) {
                $domain_name = $this->getDomainNameFromMapping($collection_name);
                if ($domain_name !== null) {
                    /**
                     * @var \Everon\Interfaces\Collection $RelationCollection
                     */
                    $RelationCollection = $Resource->getDomainEntity()->getRelationCollectionByName($domain_name);
                    $relation_list = $RelationCollection->toArray();

                    $RelationCollection = new Helper\Collection([]);
                    for ($a=0 ;$a<count($relation_list); $a++) {
                        $CollectionEntity = $relation_list[$a];
                        $RelationCollection->set($a, $this->buildResourceFromEntity($CollectionEntity, $collection_name, $version));
                    }

                    $link = $this->getResourceUrl($version, $resource_name, $resource_id, $collection_name);
                    $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $version, $link, $RelationCollection);
                    $CollectionResource->setLimit($this->getRequest()->getGetParameter('limit', 10));
                    $CollectionResource->setOffset($this->getRequest()->getGetParameter('offset', 0));

                    $Resource->setRelationCollectionByName($collection_name, $CollectionResource);
                }
            }
            
            return $Resource;
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('Resource: "%s" not found', [$this->getRequest()->getUrl()], $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCollectionResource($resource_name, $version, Interfaces\ResourceNavigator $Navigator)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $link = $this->getResourceUrl($version, $resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $EntityRelationCriteria = new Criteria();
            $EntityRelationCriteria->limit($Navigator->getLimit());
            $EntityRelationCriteria->offset($Navigator->getOffset());
            
            $entity_list = $Repository->getList($EntityRelationCriteria);
    
            $ResourceList = new Helper\Collection([]);
            for ($a=0; $a<count($entity_list); $a++) {
                $CollectionEntity = $entity_list[$a];
                $ResourceList->set($a, $this->buildResourceFromEntity($CollectionEntity, $resource_name, $version));
            }
            
            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $version, $link, $ResourceList); //todo: change version to href
            $CollectionResource->setLimit($EntityRelationCriteria->getLimit());
            $CollectionResource->setOffset($EntityRelationCriteria->getOffset());
            return $CollectionResource;
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('CollectionResource: "%s" not found', [$this->getResourceUrl($version, $resource_name)], $e);
        }
    }

    public function buildResourceRelations(Interfaces\Resource $Resource, $resource_id, $resource_name, $version)
    {
        /**
         * @var \Everon\Domain\Interfaces\RestZone\Entity $Entity
         */
        $Entity = $Resource->getDomainEntity();
        $RelationCollection = new Helper\Collection([]);
        foreach ($Entity->getRelationCollection() as $domain_name => $Collection) {
            $name = $this->getResourceNameFromMapping($domain_name);
            $link = $this->getResourceUrl($version, $resource_name, $resource_id, $name);
            $RelationCollection->set($name, ['href' => $link]);
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
    public function getResourceUrl($version, $resource_name, $resource_id=null, $collection=null, $request_path=null)
    {
        $version = $version ?: $this->current_version;
        $Href = new Href($this->url, $version, $this->versioning);
        $link = $Href->getLink($resource_name, $resource_id, $collection, $request_path);
        return $link;
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
        foreach ($this->MappingCollection as $name => $value) {
            if ($domain_name === $value) {
                return $name;
            }
        }
        throw new Exception\Manager('Invalid rest mapping resource: "%s"', $domain_name);
    }

}