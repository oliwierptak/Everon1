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

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces\Config;
use Everon\Interfaces\Collection;
use Everon\View;

abstract class Mvc extends \Everon\Module\AbstractModule implements Interfaces\Mvc
{
    use View\Dependency\Injection\ViewManager;
    
    /**
     * @var Collection
     */
    protected $ViewCollection = null;
    
    /**
     * @param $name
     * @param $module_directory
     * @param Config $Config
     */
    public function __construct($name, $module_directory, Config $Config)
    {
        parent::__construct($name, $module_directory, $Config);
        $this->ViewCollection = new Helper\Collection([]);
    }

    /**
     * @param $layout_name
     * @param $view_name
     * @return View\Interfaces\View
     */
    protected function createView($layout_name, $view_name)
    {
        $TemplateDirectory = new \SplFileInfo(implode(DIRECTORY_SEPARATOR, [
            $this->getDirectory().'View', $view_name, 'templates'
        ]));
        
        if ($TemplateDirectory->isDir() === false) {
            $template_directory = null;
        }
        else {
            $template_directory = $TemplateDirectory->getPathname().DIRECTORY_SEPARATOR;
        }
        
        try {
            $namespace = 'Everon\Module\\'.$this->getName().'\View';
            $class_name = $this->getFactory()->getFullClassName($namespace, $view_name);
            $this->getFactory()->classExists($class_name);
            $View = $this->getViewManager()->createView($view_name, $template_directory, $namespace);
        }
        catch (Exception\Factory $e) {
            //fallback to default in case no view exists
            $View = $this->getViewManager()->createView('Base', $template_directory, 'Everon\View');
            $View->setName($view_name);
        }
            
        return $View;
    }

    /**
     * @inheritdoc
     */
    public function getViewByName($layout_name, $view_name)
    {  
        $key = $layout_name.$view_name;
        if ($this->ViewCollection->has($key) === false) {
            $View = $this->createView($layout_name, $view_name);
            $this->ViewCollection->set($key, $View);
        }
        
        return $this->ViewCollection->get($key);
    }

    /**
     * @inheritdoc
     */
    public function setViewByViewName($layout_name, View\Interfaces\View $View)
    {
        $key = $layout_name.$View->getName();
        $this->ViewCollection->set($key, $View);
    }
}