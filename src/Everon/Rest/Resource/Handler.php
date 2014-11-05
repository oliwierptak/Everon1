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

use Everon\Dependency;
use Everon\Domain;
use Everon\Exception as EveronException;
use Everon\Helper;
use Everon\Http;
use Everon\Rest\Exception;
use Everon\Rest\Interfaces;

class Handler implements Interfaces\ResourceHandler
{
    use Dependency\Injection\Factory;
    use Domain\Dependency\Injection\DomainManager;
    
    use Helper\AlphaId;
    use Helper\Arrays;
    use Helper\Asserts\IsInArray;
    use Helper\Asserts\IsNull;
    use Helper\Exceptions;

    const VERSIONING_URL = 'url';
    const VERSIONING_HEADER = 'header';
    const ALPHA_ID_SALT = 'aVg656';
    const DEFAULT_COLLECTION_SIZE = 10;

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
     * @param Domain\Interfaces\Entity $Entity
     * @param $version
     * @param $resource_name
     * @return Interfaces\Resource
     * @throws \Everon\Rest\Exception\Resource
     */
    public function buildResourceFromEntity(Domain\Interfaces\Entity $Entity, $version, $resource_name)
    {
        $this->assertIsInArray($version, $this->supported_versions, 'Unsupported version: "%s"', 'Everon\Rest\Exception\Request');
        
        $domain_name = $this->getDomainNameFromMapping($resource_name);
        $resource_id = $this->generateResourceId($Entity->getId(), $domain_name);
        
        $Href = $this->getResourceUrl($version, $resource_name, $resource_id);
        $Resource = $this->getFactory()->buildRestResource($domain_name, $version, $Href, $resource_name, $Entity);
        $this->buildResourceRelations($Resource);

        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function add($version, $resource_name, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Entity = $Model->create($data);
            $Model->{'add'.$domain_name}($Entity, $user_id);
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
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $data = $this->arrayMergeDefault($Entity->toArray(), $data);
            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Entity = $Model->create($data);
            $Model->{'save'.$domain_name}($Entity, $user_id);
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
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Model->{'delete'.$domain_name}($Entity, $user_id);
            return $this->buildResourceFromEntity($Entity, $version, $resource_name);
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function addCollection($version, $resource_name, $resource_id, $collection_name, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $relation_domain_name = $this->getDomainNameFromMapping($collection_name);
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $Resource =  $this->buildResourceFromEntity($Entity, $version, $resource_name);
            
            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Model->addCollection($Resource->getDomainEntity(), $relation_domain_name, $data, $user_id);
            return $Resource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }   
    }

    /**
     * @inheritdoc
     */
    public function saveCollection($version, $resource_name, $resource_id, $collection_name, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $relation_domain_name = $this->getDomainNameFromMapping($collection_name);
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $Resource =  $this->buildResourceFromEntity($Entity, $version, $resource_name);

            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Model->saveCollection($Resource->getDomainEntity(), $relation_domain_name, $data, $user_id);
            return $Resource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteCollection($version, $resource_name, $resource_id, $collection_name, array $data, $user_id)
    {
        try {
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $relation_domain_name = $this->getDomainNameFromMapping($collection_name);
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $id = $this->generateEntityId($resource_id, $domain_name);
            $Entity = $Repository->getEntityById($id);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            $Resource =  $this->buildResourceFromEntity($Entity, $version, $resource_name);

            $Model = $this->getDomainManager()->getModelByName($domain_name);
            $Model->deleteCollection($Resource->getDomainEntity(), $relation_domain_name, $data, $user_id);
            return $Resource;
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
            $EntityRelationCriteria = $Navigator->toCriteria();            
            $domain_name = $this->getDomainNameFromMapping($resource_name);
            $id = $this->generateEntityId($resource_id, $domain_name);

            $Entity = $this->getDomainManager()->getRepositoryByName($domain_name)->getEntityById($id, $EntityRelationCriteria);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $resource_id), 'Domain');
            
            $Resource = $this->buildResourceFromEntity($Entity, $version, $resource_name);
            $Href = $this->getResourceUrl($version, $resource_name, $resource_id);
            $Resource->setHref($Href);
            $this->expandResource($Resource, $Navigator);
            
            return $Resource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function expandResource(Interfaces\Resource $Resource, Interfaces\ResourceNavigator $Navigator)
    {
        $resources_to_expand = $Navigator->getExpand() ?: [];
        sort($resources_to_expand);
        foreach ($resources_to_expand as $collection_name) {
            /**
             * @var \Everon\Rest\Interfaces\ResourceCollection $ResourceCollection
             * @var \Everon\Domain\Interfaces\Relation $EntityRelation
             * @var \Everon\Domain\Interfaces\Entity $Entity
             */
            $extra_expand = null;
            if (strpos($collection_name, '.') !== false) {
                $tokens = explode('.', $collection_name); //eg. foo.bar.zzz
                $collection_name = array_shift($tokens);
                $extra_expand = implode('.', $tokens);
                $Navigator->setExpand([$collection_name]);
            }
            
            $domain_name = $this->getDomainNameFromMapping($collection_name);
            $EntityRelation = $Resource->getDomainEntity()->getRelationByName($domain_name);
            if ($EntityRelation === null) {
                throw new Exception\Resource('Invalid entity relation for: "%s"', $domain_name);
            }
            
            $Paginator = $this->getFactory()->buildPaginator(
                $EntityRelation->getCount(),
                $Navigator->getOffset(),
                $Navigator->getLimit()
            );
            
            $ResourceCollection = $Resource->getRelationCollectionByName($collection_name);
            
            if ($extra_expand !== null && $ResourceCollection instanceof Interfaces\ResourceCollection) {
                foreach ($ResourceCollection->getItemCollection() as $ResourceItemToExpand) { //DRY
                    $NavigatorToExpand = clone $Navigator;
                    //$NavigatorToExpand->setLimit($Paginator->getLimit());
                    //$NavigatorToExpand->setOffset($Paginator->getOffset());
                    $NavigatorToExpand->setLimit(self::DEFAULT_COLLECTION_SIZE);
                    $NavigatorToExpand->setOffset(0);
                    $NavigatorToExpand->setOrderBy([]);
                    $NavigatorToExpand->setExpand([$extra_expand]);
                    $this->expandResource($ResourceItemToExpand, $NavigatorToExpand);
                }
                continue;
            }
            
            if ($EntityRelation === null) {
                continue;
            }
            
            $a = 0;
            $ResourceCollection = new Helper\Collection([]);
            $data = $EntityRelation->getData($Navigator->toCriteria())->toArray();
            foreach ($data as $Entity) {
                $Item = $this->buildResourceFromEntity($Entity, $Resource->getVersion(), $collection_name);
                $resource_id = $this->generateResourceId($Entity->getId(), $collection_name);
                $Href = $this->getResourceUrl($Item->getVersion(), $Resource->getName(), $resource_id);

                if ($Resource->getHref()->getItemId() === null) { //revert to base resource url without relations when not null
                    $Href->setCollectionName($collection_name);
                    $Href->setItemId($resource_id);
                }
                
                $Item->setHref($Href);
                $ResourceCollection->set($a++, $Item);
            }

            if ($extra_expand !== null) {
                foreach ($ResourceCollection as $ResourceItemToExpand) { //DRY
                    $NavigatorToExpand = clone $Navigator;
                    //$NavigatorToExpand->setLimit($Paginator->getLimit());
                    //$NavigatorToExpand->setOffset($Paginator->getOffset());
                    $NavigatorToExpand->setLimit(self::DEFAULT_COLLECTION_SIZE);
                    $NavigatorToExpand->setOffset(0);
                    $NavigatorToExpand->setOrderBy([]);
                    $NavigatorToExpand->setExpand([$extra_expand]);
                    $this->expandResource($ResourceItemToExpand, $NavigatorToExpand);
                }
            }

            $Href = clone $Resource->getHref();
            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $Href, $ResourceCollection, $Paginator);
            $CollectionResource->getHref()->setCollectionName($collection_name);
            $CollectionResource->setLimit($Paginator->getLimit());
            $CollectionResource->setOffset($Paginator->getOffset());

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
            $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
            $EntityRelationCriteria = $Navigator->toCriteria();
            $Paginator = $this->getFactory()->buildPaginator($Repository->count($EntityRelationCriteria), $Navigator->getOffset(), $Navigator->getLimit());
            $entity_list = $Repository->getByCriteria($EntityRelationCriteria);
    
            $ResourceList = new Helper\Collection([]);
            for ($a=0; $a<count($entity_list); $a++) {
                $CollectionEntity = $entity_list[$a];
                $Resource = $this->buildResourceFromEntity($CollectionEntity, $version, $resource_name);
                
                $NavigatorToExpand = clone $Navigator;
                //$NavigatorToExpand->setLimit($Paginator->getLimit());
                //$NavigatorToExpand->setOffset($Paginator->getOffset());
                $NavigatorToExpand->setLimit(self::DEFAULT_COLLECTION_SIZE);
                $NavigatorToExpand->setOffset(0);
                $NavigatorToExpand->setOrderBy([]);
                
                $this->expandResource($Resource, $NavigatorToExpand);
                $ResourceList->set($a, $Resource);
            }

            $CollectionResource = $this->getFactory()->buildRestCollectionResource($domain_name, $Href, $ResourceList, $Paginator);
            $CollectionResource->setLimit($EntityRelationCriteria->getLimit());
            $CollectionResource->setOffset($EntityRelationCriteria->getOffset());
            return $CollectionResource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function getCollectionItemResource($version, $resource_name, $resource_id, $collection, $item_id, Interfaces\ResourceNavigator $Navigator)
    {
        try {
            $EntityRelationCriteria = $Navigator->toCriteria();
            $domain_name = $this->getDomainNameFromMapping($collection);
            $id = $this->generateEntityId($item_id, $domain_name);
            
            $Entity = $this->getDomainManager()->getRepositoryByName($domain_name)->getEntityById($id, $EntityRelationCriteria);
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" with id: "%s" not found', $domain_name, $item_id), 'Domain');

            $Resource = $this->buildResourceFromEntity($Entity, $version, $collection);
            $Href = $this->getResourceUrl($version, $resource_name, $resource_id, $collection);
            $Href->setItemId($item_id);
            $Resource->setHref($Href);
            
            $expand = $Navigator->getExpand();
            foreach ($expand as $index => $item_name) {
                if ($item_name === $collection) {
                    unset($expand[$index]);
                }
            }
            
            $Navigator->setExpand($expand);
            $this->expandResource($Resource, $Navigator);

            return $Resource;
        }
        catch (EveronException\Domain $e) {
            throw new Exception\Resource($e->getMessage());
        }
    }

    /**
     * @param Interfaces\Resource $Resource
     */
    protected function buildResourceRelations(Interfaces\Resource $Resource)
    {
        /**
         * @var \Everon\Domain\Interfaces\RestZone\Entity $Entity
         */
        $Entity = $Resource->getDomainEntity();
        $RelationCollection = new Helper\Collection([]);
        foreach ($Entity->getRelationCollection() as $domain_name => $Collection) {
            $name = $this->getResourceNameFromMapping($domain_name);
            $link = $Resource->getHref()->getLink($name);
            $RelationCollection->set($name, ['href' => $link]);
        }
        $Resource->setRelationCollection($RelationCollection);
    }

    /**
     * @inheritdoc
     */
    public function generateEntityId($resource_id, $domain_name)
    {
        return $resource_id;
        $domain_name .= static::ALPHA_ID_SALT;
        return $this->alphaId($resource_id, true, 7, $domain_name);
    }

    /**
     * @inheritdoc
     */
    public function generateResourceId($entity_id, $domain_name)
    {
        return $entity_id;
        $domain_name .= static::ALPHA_ID_SALT;
        return $this->alphaId($entity_id, false, 7, $domain_name);
    }

    /**
     * @inheritdoc
     */
    public function getResourceUrl($version, $resource_name, $resource_id=null, $collection=null, $request_path=null)
    {
        $version = $version ?: $this->current_version;
        
        $Href = $this->getFactory()->buildRestResourceHref($this->url, $version, $this->versioning);
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
        $domain_name = $this->getMappingCollection()->get($resource_name, null);
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

    /**
     * @inheritdoc
     */
    public function getMappingCollection()
    {
        return $this->MappingCollection;
    }

    /**
     * @inheritdoc
     */
    public function setMappingCollection(\Everon\Interfaces\Collection $MappingCollection)
    {
        $this->MappingCollection = $MappingCollection;
    }
    
}