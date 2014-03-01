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

use Everon\Helper;


abstract class Resource implements Interfaces\Resource
{
    use Helper\ToArray;
    
    protected $resource_name = null;
    protected $resource_href = null;
    protected $resource_version = null;
    
    abstract protected function init();


    public function __construct($name, $version, $data=null)
    {
        $this->resource_name = $name;
        $this->resource_version = $version;
        $this->data = $data;
    }

    /**
     * @param $version
     */
    public function setResourceVersion($version)
    {
        $this->resource_version = $version;
    }

    /**
     * @inheritdoc
     */
    public function getResourceVersion()
    {
        return $this->resource_version;
    }

    /**
     * @param $href
     */
    public function setResourceHref($href)
    {
        $this->resource_href = $href;
    }

    /**
     * @inheritdoc
     */
    public function getResourceHref()
    {
        return $this->resource_href;
    }

    /**
     * @param $name
     */
    public function setResourceName($name)
    {
        $this->resource_name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getResourceName()
    {
        return $this->resource_name;
    }
    
    public function toJson()
    {
        return json_encode([$this->toArray(true)], \JSON_FORCE_OBJECT);
    }
    
    public function getToArray()
    {
        $this->init();
        $this->data['href'] = $this->getResourceHref();
        return $this->data;
    }
}
