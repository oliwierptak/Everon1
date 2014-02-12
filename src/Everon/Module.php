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

abstract class Module implements Interfaces\Module
{
    use Dependency\Config;
    use Dependency\Injection\Factory;
    use Dependency\Injection\ViewManager;
    
    protected $name = null;
    
    protected $directory = null;

    /**
     * @var Interfaces\ConfigItemRouter
     */
    protected $RouteConfig = null;

    /**
     * @var Interfaces\Collection
     */
    protected $ViewCollection = null;
    
    /**
     * @var Interfaces\Collection
     */
    protected $ControllerCollection = null;

    
    public function __construct($name, $module_directory, Interfaces\Config $Config, Interfaces\Config $RouterConfig)
    {
        $this->name = $name;
        $this->directory = $module_directory;
        $this->Config = $Config;
        $this->RouteConfig = $RouterConfig;
        $this->ViewCollection = new Helper\Collection([]);
        $this->ControllerCollection = new Helper\Collection([]);
    }

    /**
     * @param $name
     * @return Interfaces\Controller
     */
    protected function createController($name)
    {
        return $this->getFactory()->buildController($name, $this, 'Everon\Module\\'.$this->getName().'\Controller');
    }
    
    protected function createView($name)
    {
        $template_directory = $this->getDirectory().'View'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        return $this->getViewManager()->createView($name, $template_directory, 'Everon\Module\\'.$this->getName().'\View');
    }

    public function getController($name)
    {
        if ($this->ControllerCollection->has($name) === false) {
            $View = $this->createController($name);
            $this->ControllerCollection->set($name, $View);
        }

        return $this->ControllerCollection->get($name);
    }

    public function getView($name)
    {
        if ($this->ViewCollection->has($name) === false) {
            $View = $this->createView($name);
            $this->ViewCollection->set($name, $View);
        }
        
        return $this->ViewCollection->get($name);
    }

    /**
     * @param null $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return null
     */
    public function getDirectory()
    {
        return $this->directory;
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
