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
    
    
    
    public function __construct($url, $version, $versioning)
    {
        $this->url = $url;
        $this->versions = $version;
    }
    
    public function getResource($resource_id, $name, $version)
    {
        $id = $this->generateEntityId($resource_id, $name);
        $Repository = $this->getDomainManager()->getRepository($name);
        $Entity = $Repository->fetchEntityById($id);
        return $this->getFactory()->buildRestResource($name, $version, $Entity);
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
    
    public function generateHref($resource_id, $name)
    {
        return $this->url.$name.'/'.$resource_id;
    }
    
}