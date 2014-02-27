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
use Everon\Interfaces\Collection;
use Everon\Helper;
use Everon\Http;
use Everon\Rest\Interfaces;

class Manager implements Interfaces\ResourceManager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\DomainManager;
    use Helper\AlphaId;
    
    const VERSIONING_URL = 'url';
    const VERSIONING_HEADER = 'header';
    
    const ALPHA_ID_SALT = 'Vhg656';
    
    /**
     * @var Collection
     */
    protected $ResourceCollection = null;
    
    protected $versions = ['v1'];

    /**
     * @var string Accepted values are: 'url' or 'header'
     */
    protected $versioning = 'url';
    
    protected $current_version = 'v1';
    
    protected $url = null;
    
    protected $generated_url = null;
    
    
    
    public function __construct($url, $version, $versioning)
    {
        $this->url = $url;
        $this->versions = $version;
    }

    /**
     * @inheritdoc
     */
    public function getResource($resource_id, $name, $version)
    {
        try {
            $id = $this->generateEntityId($resource_id, $name);
            $Repository = $this->getDomainManager()->getRepository($name);
            $Entity = $Repository->fetchEntityById($id);
            return $this->getFactory()->buildRestResource($name, $version, $Entity);
        }
        catch (\Exception $e) {
            throw new Http\Exception\NotFound('Resource: "%s" not found', $this->getResourceUrl($resource_id, $name));
        }
    }
    
    public function generateEntityId($resource_id, $name)
    {
        $name .= static::ALPHA_ID_SALT;
        return $this->alphaId($resource_id, true, 7, $name);
    }
    
    public function generateResourceId($entity_id, $name)
    {
        $name .= static::ALPHA_ID_SALT;
        return $this->alphaId($entity_id, false, 7, $name);
    }
    
    public function getResourceUrl($resource_id, $name)
    {
        return $this->getUrl().$name.'/'.$resource_id;
    }
    
    public function getUrl()
    {
        switch ($this->versioning) {
            case static::VERSIONING_URL:
                return $this->url.$this->current_version;        
                break;
            
            default:
                return $this->url;
                break;
        }
    }
    
}