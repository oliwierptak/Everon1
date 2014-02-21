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
    
    protected $url = '/v1/api/';
    
    
    public function getResource($resource_id, $name)
    {
        //$this->getFactory()->buildRestResource($name, $this->current_version, $data);
        //entity = User
        //resource = Users
        //cut off plural
        $id = $this->generateEntityId($resource_id, $name);
        $name = substr($name, 0, strlen($name) - 1);
        $Repository = $this->getDomainManager()->getRepository($name);
        $data = $Repository->fetchEntityById($id);
        $this->getDomainManager()->getEntity($name, $id, $data);
        
    }
    
    public function generateEntityId($resource_id, $name)
    {

    }
    
    public function generateResourceId($entity_id, $name)
    {

    }
    
}