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
use Everon\Exception;
use Everon\Helper;
use Everon\Http;
use Everon\Rest\Interfaces;

class Handler implements Interfaces\ResourceManager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\DomainManager;
    use Helper\AlphaId;
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
    
    protected $current_version = null;  //v1, v2, v3...
    
    protected $url = null;
    

    /**
     * @param $url
     * @param $version
     * @param $versioning
     */
    public function __construct($url, $version, $versioning)
    {
        $this->url = $url;
        $this->current_version = $version;
        $this->versioning = $versioning;
    }

    /**
     * @inheritdoc
     */
    public function getResource($resource_id, $name, $section=null, $version=null)
    {
        try {
            $id = $this->generateEntityId($resource_id, $name);
            $Repository = $this->getDomainManager()->getRepository($name);
            $version = $version ?: $this->current_version;
            $Entity = $Repository->getEntityById($id);
            $href = $this->getResourceUrl($resource_id, $name, $section);
            
            $this->assertIsNull($Entity, sprintf('Domain Entity: "%s" not found', $id), 'Domain');
            $this->assertIsInArray($version, $this->supported_versions, 'Unsupported version: "%s"', 'Domain');
            
            $Resource = $this->getFactory()->buildRestResource($name, $version, $Entity->toArray(), 'Everon\Rest\Resource');
            $Resource->setResourceHref($href);
            
            return $Resource;
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('Resource: "%s" not found', [$this->getResourceUrl($resource_id, $name, $section)], $e);
        }
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
    public function getResourceUrl($resource_id, $name, $section=null)
    {
        $name = strtolower($name).'s';
        $section =  $section !== null ? '/'.$section : '';
        return $this->getUrl().$name.'/'.$resource_id.$section;
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
    
}