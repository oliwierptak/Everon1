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
use Everon\Exception as EveronException;
use Everon\Helper;
use Everon\Http;
use Everon\Rest\Exception;
use Everon\Rest\Interfaces;

class Handler implements Interfaces\ResourceHandler
{
    use Dependency\Injection\Factory;
    use \Everon\Domain\Dependency\Injection\DomainManager;
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
    protected $supported_versions = null;   //[v1, v2, v3, ...]

    /**
     * @var string Versioning type. Accepted values are: 'url' or 'header'
     */
    protected $versioning = null;
    
    protected $current_version = null;  //v1
    
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
     * @param $version
     * @param $resource_name
     * @return Interfaces\Resource
     * @throws \Everon\Rest\Exception\Resource
     */
    public function buildResourceFromEntity(Entity $Entity, $version, $resource_name)
    {
        $this->assertIsInArray($version, $this->supported_versions, 'Unsupported version: "%s"', 'Domain');
        
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $resource_id = $this->generateResourceId($Entity->getId(), $resource_name);

        $Href = new Href($this->url, $version, $this->versioning);
        $Href->setCollectionName('');
        $Href->setResourceName($resource_name);
        $Href->setResourceId($resource_id);
        
        $Resource = $this->getFactory()->buildRestResource($domain_name, $version, $Href, $resource_name, $Entity); //todo: change version to href
        $this->buildResourceRelations($Resource, $version, $resource_name, $resource_id);

        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function add($version, $resource_name, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $Entity = $Repository->persistFromArray($data, $user_id);
            return $this->buildResourceFromEntity($Entity, $version, $resource_name);
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }
    
    /**
     * @inheritdoc
     */
    public function save($version, $resource_name, $resource_id, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $id = $this->generateEntityId($resource_id, $resource_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $data = $this->arrayMergeDefault($Entity->toArray(), $data);
            $Entity = $Repository->persistFromArray($data, $user_id);
            return $this->buildResourceFromEntity($Entity, $version, $resource_name);
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($version, $resource_name, $resource_id, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $Repository->remove($Entity, $user_id);
            return $this->buildResourceFromEntity($Entity, $version, $resource_name);
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function getResource($version, $resource_name, $resource_id, Interfaces\ResourceNavigator $Navigator)
    {
        try {
            $EntityRelationCriteria = new Criteria();
            $EntityRelationCriteria->limit($Navigator->getLimit());
            $EntityRelationCriteria->offset($Navigator->getOffset());
            
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $this->getDomainManager()->getRepository($domain_name)->getEntityById($id, $EntityRelationCriteria);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            
            $Resource = $this->buildResourceFromEntity($Entity, $version, $resource_name);
            $Href = $this->getResourceUrl($version, $resource_name, $resource_id);
            $Resource->setHref($Href);
            $resources_to_expand = $Navigator->getExpand();
            $this->expandResource($Resource, $resources_to_expand);
            
            return $Resource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function expandResource(Interfaces\Resource $Resource, array $resources_to_expand)
    {
        foreach ($resources_to_expand as $collection_name) {
            $domain_name = $this->getDomainNameFromMapping($collection_name);
            /**
             * @var \Everon\Interfaces\Collection $RelationCollection
             */
            $RelationCollection = $Resource->getDomainEntity()->getRelationCollectionByName($domain_name);
            $relation_list = $RelationCollection->toArray();

            $RelationCollection = new Helper\Collection([]);
            for ($a=0 ;$a<count($relation_list); $a++) {
                $CollectionEntity = $relation_list[$a];
                $RelationCollection->set($a, $this->buildResourceFromEntity($CollectionEntity, $Resource->getVersion(), $collection_name));
            }

            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $Resource->getHref(), $RelationCollection);
            $CollectionResource->setLimit($this->getRequest()->getGetParameter('limit', 10));
            $CollectionResource->setOffset($this->getRequest()->getGetParameter('offset', 0));

            $Resource->setRelationCollectionByName($collection_name, $CollectionResource);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCollectionResource($version, $resource_name, Interfaces\ResourceNavigator $Navigator)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $Href = $this->getResourceUrl($version, $resource_name);
            $Repository = $this->getDomainManager()->getRepository($domain_name);
            $EntityRelationCriteria = new Criteria();
            $EntityRelationCriteria->limit($Navigator->getLimit());
            $EntityRelationCriteria->offset($Navigator->getOffset());
            $entity_list = $Repository->getList($EntityRelationCriteria);
    
            $ResourceList = new Helper\Collection([]);
            for ($a=0; $a<count($entity_list); $a++) {
                $CollectionEntity = $entity_list[$a];
                $ResourceList->set($a, $this->buildResourceFromEntity($CollectionEntity, $version, $resource_name));
            }
            
            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $Href, $ResourceList);
            $CollectionResource->setLimit($EntityRelationCriteria->getLimit());
            $CollectionResource->setOffset($EntityRelationCriteria->getOffset());
            return $CollectionResource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    public function buildResourceRelations(Interfaces\Resource $Resource, $version, $resource_name, $resource_id)
    {
        /**
         * @var \Everon\Domain\Interfaces\RestZone\Entity $Entity
         */
        $Entity = $Resource->getDomainEntity();
        $RelationCollection = new Helper\Collection([]);
        foreach ($Entity->getRelationCollection() as $domain_name => $Collection) {
            $name = $this->getResourceNameFromMapping($domain_name);
            $link = $Resource->getHref()->getLink();
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
        $Href->setCollectionName($collection);
        $Href->setResourceName($resource_name);
        $Href->setResourceId($resource_id);
        $Href->setRequestPath($request_path);
        return $Href;
    }
    
    /**
     * @inheritdoc
     */
    public function getDomainNameFromMapping($resource_name)
    {
        $domain_name = $this->MappingCollection->get($resource_name, null);
        if ($domain_name === null) {
            throw new Exception\Resource('Invalid rest mapping domain: "%s"', $resource_name);
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
        throw new Exception\Resource('Invalid rest mapping resource: "%s"', $domain_name);
    }

}