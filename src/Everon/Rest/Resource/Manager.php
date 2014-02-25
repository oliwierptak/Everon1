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
use Everon\Rest\Interfaces;

class Manager implements Interfaces\ResourceManager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\DomainManager;
    
    const RANDSALT = '#$@#$#FSFSDF22edfa';
    
    
    /**
     * @var Collection
     */
    protected $ResourceCollection = null;
    
    protected $versions = ['v1'];
    
    protected $current_version = 'v1';
    
    protected $url = null;
    
    public function __construct($url, $version)
    {
        
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
        return 1;
    }
    
    public function generateResourceId($entity_id, $name)
    {
        return 'aabbcc';
    }
    
    public function generateHref($resource_id, $name)
    {
        
    }
    
}