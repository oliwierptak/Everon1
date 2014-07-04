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
use Everon\Helper;
use Everon\Interfaces\Config;
use Everon\Interfaces\Collection;
use Everon\View;

abstract class Mvc extends \Everon\Module implements Interfaces\Mvc
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
     * @param $name
     * @return View\Interfaces\View
     */
    protected function createView($name)
    {
        $template_directory = $this->getDirectory().'View'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        return $this->getViewManager()->createView($name, $template_directory, 'Everon\Module\\'.$this->getName().'\View');
    }

    /**
     * @inheritdoc
     */
    public function getViewByName($name)
    {
        if ($this->ViewCollection->has($name) === false) {
            $View = $this->createView($name);
            $this->ViewCollection->set($name, $View);
        }
        
        return $this->ViewCollection->get($name);
    }

    /**
     * @param View\Interfaces\View $View
     */
    public function setViewByViewName(View\Interfaces\View $View)
    {
        $this->ViewCollection->set($View->getName(), $View);
    }
}