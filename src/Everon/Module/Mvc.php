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
use Everon\Interfaces\View;

abstract class Mvc extends \Everon\Module implements Interfaces\Mvc
{
    use Dependency\Injection\ViewManager;
    
    /**
     * @var Collection
     */
    protected $ViewCollection = null;
    
    /**
     * @param $name
     * @param $module_directory
     * @param Config $Config
     * @param Config $RouterConfig
     */
    public function __construct($name, $module_directory, Config $Config, Config $RouterConfig)
    {
        parent::__construct($name, $module_directory, $Config, $RouterConfig);
        $this->ViewCollection = new Helper\Collection([]);
    }

    /**
     * @param $name
     * @return View
     */
    protected function createView($name)
    {
        $template_directory = $this->getDirectory().'View'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        return $this->getViewManager()->createView($name, $template_directory, 'Everon\Module\\'.$this->getName().'\View');
    }

    /**
     * @inheritdoc
     */
    public function getView($name)
    {
        if ($this->ViewCollection->has($name) === false) {
            $View = $this->createView($name);
            $this->ViewCollection->set($name, $View);
        }
        
        return $this->ViewCollection->get($name);
    }
    
}