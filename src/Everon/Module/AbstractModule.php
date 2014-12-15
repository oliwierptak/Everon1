<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module;

use Everon\Helper;
use Everon\Interfaces;

abstract class AbstractModule implements \Everon\Module\Interfaces\Module, \Everon\Interfaces\Dependency\GetUrl
{
    use \Everon\Dependency\Config;
    use \Everon\Dependency\Injection\Factory;
    use \Everon\Dependency\Injection\Router;
    use Dependency\Injection\ModuleManager;

    use Helper\GetUrl;
    
    
    protected $name = null;
    
    protected $directory = null;
    
    /**
     * @var Interfaces\Collection
     */
    protected $ControllerCollection = null;
    
    /**
     * @var Interfaces\Collection
     */
    protected $AjaxControllerCollection = null;

    /**
     * @var Interfaces\FactoryWorker
     */
    protected $FactoryWorker = null;
    
    
    /**
     * @param $name
     * @param $module_directory
     * @param Interfaces\Config $Config
     */
    public function __construct($name, $module_directory, Interfaces\Config $Config)
    {
        $this->name = $name;
        $this->directory = $module_directory;
        $this->Config = $Config;
        $this->ControllerCollection = new Helper\Collection([]);
        $this->AjaxControllerCollection = new Helper\Collection([]);
    }

    /**
     * @param $name
     * @return Interfaces\Controller
     */
    protected function createController($name)
    {
        return $this->getFactory()->buildController($name, $this, 'Everon\Module\\'.$this->getName().'\Controller');
    }

    /**
     * @param $name
     * @return Interfaces\Controller
     */
    protected function createAjaxController($name)
    {
        return $this->getFactory()->buildController($name, $this, 'Everon\Module\\'.$this->getName().'\Controller\Ajax');
    }

    /**
     * @inheritdoc
     */
    public function getController($name)
    {
        if ($this->ControllerCollection->has($name) === false) {
            $Controller = $this->createController($name);
            $this->ControllerCollection->set($name, $Controller);
        }

        return $this->ControllerCollection->get($name);
    }

    /**
     * @inheritdoc
     */
    public function getAjaxController($name)
    {
        if ($this->AjaxControllerCollection->has($name) === false) {
            $AjaxController = $this->createAjaxController($name);
            $this->AjaxControllerCollection->set($name, $AjaxController);
        }

        return $this->AjaxControllerCollection->get($name);
    }

    /**
     * @inheritdoc
     */
    public function setFactoryWorker(Interfaces\FactoryWorker $FactoryWorker)
    {
        $this->FactoryWorker = $FactoryWorker;
    }

    /**
     * @inheritdoc
     */
    public function getFactoryWorker()
    {
        return $this->FactoryWorker;
    }

    /**
     * @inheritdoc
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }
    
    /**
     * @inheritdoc
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @inheritdoc
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
    
    public function setup()
    {
        
    }
}
