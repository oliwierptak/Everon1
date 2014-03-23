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

use Everon\Rest\Dependency;
use Everon\Helper;
use Everon\Rest\Exception;
use Everon\Rest\Interfaces;

class Href implements Interfaces\ResourceHref
{
    protected $url = null;
    protected $version = null;
    protected $resource = null;
    protected $resource_id = null;
    protected $collection_name = null;
    protected $versioning = null;


    /**
     * @param $url
     * @param $version
     * @param $versioning
     */
    public function __construct($url, $version, $versioning)
    {
        $this->url = $url;
        $this->version = $version;
        $this->versioning = $versioning;
    }

    /**
     * @inheritdoc
     */
    public function getLink($resource_name, $resource_id='', $collection='', $request_path='')
    {
        $request_path = trim($request_path) !== '' ? '?'.$request_path : $request_path;
        $params = '/'.rtrim(implode('/', [$resource_name,$resource_id,$collection]),'/').$request_path;
        switch ($this->versioning) {
            case Handler::VERSIONING_URL:
                return $this->url.$this->version.$params;
                break;

            case Handler::VERSIONING_HEADER:
                return $this->url.$params;
                break;
        }
        
        throw new Exception\Resource('Invalid versioning type: "%s"', $this->versioning);
    }

    /**
     * @param $collection
     */
    public function setCollectionName($collection)
    {
        $this->collection_name = $collection;
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collection_name;
    }

    /**
     * @param $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param $resource_id
     */
    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $versioning
     */
    public function setVersioning($versioning)
    {
        $this->versioning = $versioning;
    }

    /**
     * @return string
     */
    public function getVersioning()
    {
        return $this->versioning;
    }
}
