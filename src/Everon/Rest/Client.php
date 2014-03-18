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

use Everon\Exception;
use Everon\Rest\Interfaces;
use Everon\Dependency;
use Everon\Rest\Dependency\Injection\ResourceManager as InjectResourceManager;

class Client implements Interfaces\Client
{
    use Dependency\Injection\DomainManager;
    use InjectResourceManager;
    
    /**
     * @var \Everon\Rest\Interfaces\CurlAdapter
     */
    protected $CurlAdapter;

    /**
     * @var \Everon\Rest\Interfaces\ResourceHref
     */
    protected $ResourceHref = null;

    
    public function __construct(Interfaces\ResourceHref $ResourceHref, Interfaces\CurlAdapter $CurlAdapter)
    {
        $this->ResourceHref = $ResourceHref;
        $this->CurlAdapter = $CurlAdapter;
    }

    /**
     * @inheritdoc
     */
    public function get($resource_name, $resource_id=null, $collection_name=null)
    {
        $url = $this->getUrl($resource_name, $resource_id, $collection_name);
        $result = $this->getCurlAdapter()->get($url);
        //$code = $this->getCurlAdapter()->get
        return json_decode($result, true);
    }

    /**
     * @inheritdoc
     */
    public function post($resource_name, array $data)
    {
        $url = $this->getUrl($resource_name);
        $result = $this->getCurlAdapter()->post($url, json_encode($data));
        return json_decode($result, true);
    }

    /**
     * @inheritdoc
     */
    public function put($resource_name, $resource_id, array $data)
    {
        $url = $this->getUrl($resource_name, $resource_id);
        $result = $this->getCurlAdapter()->put($url, json_encode($data));
        return json_decode($result, true);
    }


    /**
     * @inheritdoc
     */
    public function delete($resource_name, $resource_id)
    {
        $url = $this->getUrl($resource_name, $resource_id);
        $result = $this->getCurlAdapter()->delete($url);
        return json_decode($result, true);
    }

    /**
     * @inheritdoc
     */
    public function getUrl($resource_name, $resource_id=null, $collection=null)
    {
        return $this->getResourceHref()->getLink($resource_name, $resource_id, $collection);
    }

    /**
     * @param Interfaces\CurlAdapter $CurlAdapter
     */
    public function setCurlAdapter($CurlAdapter)
    {
        $this->CurlAdapter = $CurlAdapter;
    }

    /**
     * @return Interfaces\CurlAdapter
     */
    public function getCurlAdapter()
    {
        return $this->CurlAdapter;
    }

    /**
     * @param Interfaces\ResourceHref $ResourceHref
     */
    public function setResourceHref($ResourceHref)
    {
        $this->ResourceHref = $ResourceHref;
    }

    /**
     * @return Interfaces\ResourceHref
     */
    public function getResourceHref()
    {
        return $this->ResourceHref;
    }
    
}
