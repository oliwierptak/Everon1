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

use Everon\Helper;
use Everon\Rest\Interfaces;


abstract class Basic implements Interfaces\ResourceBasic
{
    use Helper\ToArray;
    
    /**
     * @var Interfaces\ResourceHref
     */
    protected $Href = null;


    /**
     * @param Interfaces\ResourceHref $Href
     */
    public function __construct(Interfaces\ResourceHref $Href)
    {
        $this->Href = $Href;
    }

    /**
     * @param $version
     */
    public function setVersion($version)
    {
        $this->getHref()->setVersion($version);
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->getHref()->getVersion();
    }

    /**
     * @inheritdoc
     */
    public function getResourceId()
    {
        return $this->getHref()->getResourceId();
    }

    /**
     * @inheritdoc
     */
    public function setResourceId($resource_id)
    {
        $this->getHref()->setResourceId($resource_id);
    }
    
    /**
     * @inheritdoc
     */
    public function setHref(Interfaces\ResourceHref $Href)
    {
        $this->Href = $Href;
    }

    /**
     * @inheritdoc
     */
    public function getHref()
    {
        return $this->Href;
    }

    /**
     * @inheritdoc
     */
    public function setName($resource_name)
    {
        $this->getHref()->setResourceName($resource_name);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getHref()->getResourceName();
    }
    /**
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode([$this->toArray()], \JSON_FORCE_OBJECT);
    }
    
    protected function getToArray()
    {
        return ['href' => $this->getHref()->getLink()];
    }
}
