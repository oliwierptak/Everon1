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
    use Helper\String\LastTokenToName;
    
    protected $name = null;
    protected $href = null;
    protected $version = null;

    public function __construct($href, $version)
    {
        $this->name = $this->stringLastTokenToName(get_class($this));
        $this->version = $version;
        $this->href = $href;
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
     * @inheritdoc
     */
    public function toJson()
    {
        return json_encode([$this->toArray()], \JSON_FORCE_OBJECT);
    }
    
    protected function getToArray()
    {
        return ['href' => $this->getHref()];
    }
}
