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
    
    protected $name = null;
    protected $href = null;
    protected $version = null;
    
    abstract protected function init();


    public function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
        $this->data = null;
    }

    /**
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @inheritdoc
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function toJson()
    {
        $this->init();
        return json_encode([$this->toArray()], \JSON_FORCE_OBJECT);
    }
    
    public function getToArray()
    {
        $this->init();
        return $this->data;
    }
}
