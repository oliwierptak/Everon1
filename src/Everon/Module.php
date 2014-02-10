<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Exception;
use Everon\Dependency;

class Module implements Interfaces\Module
{
    use Dependency\Config;
    
    protected $name = null;

    /**
     * @var Interfaces\ConfigItemRouter
     */
    protected $RouteConfig = null;
    
    
    public function __construct($name, Interfaces\Config $Config, Interfaces\Config $RouterConfig)
    {
        $this->name = $name;
        $this->Config = $Config;
        $this->RouteConfig = $RouterConfig;
    }

    /**
     * @param \Everon\Interfaces\ConfigItemRouter $RouteConfig
     */
    public function setRouteConfig(Interfaces\ConfigItemRouter $RouteConfig)
    {
        $this->RouteConfig = $RouteConfig;
    }

    /**
     * @return \Everon\Interfaces\ConfigItemRouter
     */
    public function getRouteConfig()
    {
        return $this->RouteConfig;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }
}
