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
    use Helper\ToString;
    
    protected $url = null;
    protected $version = null;
    protected $resource_name = null;
    protected $resource_id = null;
    protected $request_path = null;
    protected $collection_name = null;
    protected $item_id = null;
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
    
    protected function getToString()
    {
        return $this->getLink();
    }

    /**
     * @inheritdoc
     */
    public function getLink($custom_path='')
    {
        $request_path = (string) $this->request_path;
        $request_path = trim($request_path);
        $custom_path = trim($custom_path);
        $path = $custom_path !== '' ? $custom_path : '';

        if ($request_path !== '' && $path !== '') {
            $request_path = '/'.$path.'?'.$request_path;
        }
        else if ($request_path === '' && $path !== '') {
            $request_path = '/'.$path;
        }
        else if ($request_path !== '' && $path === '') {
            $request_path = '?'.$request_path;
        }
                
        $params = '/'.rtrim(implode('/', [$this->resource_name, $this->resource_id, $this->collection_name, $this->item_id]),'/').$request_path;
        
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
    public function setResourceName($resource)
    {
        $this->resource_name = $resource;
    }

    /**
     * @return string
     */
    public function getResourceName()
    {
        return $this->resource_name;
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
    
    /**
     * @return string
     */
    public function getRequestPath()
    {
        return $this->request_path;
    }

    /**
     * @param string $request_path
     */
    public function setRequestPath($request_path)
    {
        $this->request_path = $request_path;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * @param mixed $item_id
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;
    }
    
}
